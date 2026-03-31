<?php

declare(strict_types=1);

namespace CapMonsterClient;

use CapMonsterClient\ApiProvider\ApiProviderService;
use CapMonsterClient\ApiProvider\Request\Factory\RequestFactory;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Common\Exception\EnumResolverException;
use CapMonsterClient\Dto\Config\TimeoutConfig;
use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\ApiMethod;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\StatusTask;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\External\Dto\Response\AbstractResponse;
use CapMonsterClient\External\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\External\ExternalApiProvider;
use CapMonsterClient\Factory\DtoRequestFactory;
use CapMonsterClient\Resolver\ErrorResolver;
use CapMonsterClient\Resolver\TimeoutChecker;
use CapMonsterClient\Resolver\TransformResolver;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use CapMonsterClient\Transformer\TaskSolutionTransformer;
use DateTimeImmutable;
use JMS\Serializer\Exception\RuntimeException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

final class CapMonsterClient implements CapMonsterClientInterface
{
    private readonly SerializerBuilder $serializerBuilder;

    private string $responseContent;

    private readonly ApiProviderService $apiProvider;

    public function __construct(
        private readonly ClientInterface $psrHttpClient,
        private readonly CapMonsterConfiguration $configuration
    ) {
        //$this->serializerBuilder = new SerializerBuilder();
        $this->responseContent = '';
        $this->apiProvider = new ApiProviderService($this->psrHttpClient, $this->configuration);
    }

    /**
     * @throws CapMonsterException
     * @throws \JsonException
     */
    public function runTask(AbstractTask $task): AbstractSolution
    {
        $createTaskResponse = $this->apiProvider->createTask($task);
        $task->setTaskId($createTaskResponse->getTaskId());

        return $this->getTaskSolution($task);
    }

    /**
     * @throws CapMonsterException
     */
    public function getBalance(): float
    {
        return $this->apiProvider->getBalance();
    }

    /**
     * @throws CapMonsterException
     */
    private function sendRequest(ApiMethod $method, ?AbstractTask $task = null): void
    {
        $request = (new RequestFactory($this->serializerBuilder, $this->configuration))->create(
            $this->createDtoRequest($method, $task)
        );
        try {
            $response = $this->psrHttpClient->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new CapMonsterException(ErrorType::SEND_MESSAGE_ERROR, $exception);
        }
        $this->responseContent = (string) $response->getBody();
        $baseResponse = $this->tryToGetDtoResponse($method);
        $code = $response->getStatusCode();
        if (preg_match('~^2\d+~', (string) $code)) {
            if ($error = ErrorResolver::resolve($baseResponse)) {
                throw new CapMonsterException($error);
            }

            return;
        }

        throw new CapMonsterException(ErrorType::RESPONSE_CODE_ERROR, new \Exception($this->responseContent, $code));
    }

    /**
     * @throws CapMonsterException
     */
    private function createDtoRequest(ApiMethod $apiMethod, ?AbstractTask $task): AbstractRequest
    {
        return
            (new DtoRequestFactory())
                ->createDtoRequest($apiMethod, $this->configuration, $task)
            ;
    }

    /**
     * @throws CapMonsterException
     */
    private function tryToGetDtoResponse(ApiMethod $method): AbstractResponse
    {
        $transformer = (new TransformResolver($this->serializerBuilder))->resolve($method);
        try {
            return $transformer->transform($this->responseContent);
        } catch (RuntimeException $exception) {
            throw new CapMonsterException(ErrorType::RESPONSE_ERROR, $exception);
        }
    }

    /**
     * @throws CapMonsterException|\JsonException
     */
    private function getTaskSolution(AbstractTask $task): AbstractSolution
    {
        $timeout = $this->tryToGetTimeout($task->getType());
        $timeoutResolver = new TimeoutChecker();
        $startDateTime = new DateTimeImmutable();
        $currentDateTime = $timeoutResolver->resolve($timeout, $startDateTime);
        while (true) {
            $this->sendRequest(ApiMethod::GET_TASK_RESULT, $task);
            /** @var GetTaskResultResponse $statusTaskResponse */
            $statusTaskResponse = $this->tryToGetDtoResponse(ApiMethod::GET_TASK_RESULT);
            if ($statusTaskResponse->getStatus() === StatusTask::READY) {
                return
                    (new TaskSolutionTransformer($this->serializerBuilder))
                        ->transform($task->getType(), $this->responseContent)
                    ;
            }
            $currentDateTime = $timeoutResolver->resolve($timeout, $startDateTime, $currentDateTime);
        }
    }

    /**
     * @throws CapMonsterException
     */
    private function tryToGetTimeout(TypeTask $typeTask): TimeoutConfig
    {
        try {
            return $this->configuration->getTimeoutConfig($typeTask);
        } catch (EnumResolverException $exception) {
            throw new CapMonsterException(ErrorType::INVALID_ARGUMENT_EXCEPTION, $exception);
        }
    }
}