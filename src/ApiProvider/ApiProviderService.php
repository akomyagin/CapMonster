<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetBalanceRequest;
use CapMonsterClient\ApiProvider\Request\Factory\RequestFactory;
use CapMonsterClient\ApiProvider\Request\Factory\RequestFactoryInterface;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\External\Dto\Response\AbstractResponse;
use CapMonsterClient\External\Dto\Response\CreateTaskResponse;
use CapMonsterClient\External\Dto\Response\GetBalanceResponse;
use CapMonsterClient\External\Transformer\FromJsonTransformer;
use CapMonsterClient\Resolver\ErrorResolver;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiProviderService
{
    private readonly SerializerBuilder $serializerBuilder;

    private readonly RequestFactoryInterface $requestFactory;

    public function __construct(
        private readonly ClientInterface $psrHttpClient,
        private readonly CapMonsterConfiguration $configuration
    ) {
        $this->serializerBuilder = new SerializerBuilder();
        $this->requestFactory = new RequestFactory($this->serializerBuilder, $this->configuration);
    }

    /**
     * @throws CapMonsterException
     */
    public function getBalance(): float
    {
        $request = new GetBalanceRequest($this->configuration->getClientKey());
        $response = $this->sendRequest($request);
        /** @var GetBalanceResponse $dtoResponse */
        $dtoResponse =
            (new FromJsonTransformer(
                $this->serializerBuilder,
                GetBalanceResponse::class
            ))->transform((string) $response->getBody());
        $this->checkErrorResponse($dtoResponse);

        return $dtoResponse->getBalance();
    }

    /**
     * @throws CapMonsterException
     */
    public function createTask(AbstractTask $task, string $clientKey, ?string $callbackUrl = null): CreateTaskResponse
    {
        $request = new CreateTaskRequest($clientKey, $task, $callbackUrl);
        $response = $this->sendRequest($request);
        /** @var CreateTaskResponse $dtoResponse */
        $dtoResponse =
            (new FromJsonTransformer(
                $this->serializerBuilder,
                CreateTaskResponse::class
            ))->transform((string) $response->getBody());
        $this->checkErrorResponse($dtoResponse);

        return $dtoResponse;
    }

    public function getResultTask()
    {

    }

    /**
     * @throws CapMonsterException
     */
    private function sendRequest(AbstractRequest $dtoRequest): ResponseInterface
    {
        try {
            return $this->psrHttpClient->sendRequest(
                $this->requestFactory->create($dtoRequest)
            );
        } catch (ClientExceptionInterface $exception) {
            throw new CapMonsterException(ErrorType::SEND_MESSAGE_ERROR, $exception);
        }
    }

    /**
     * @throws CapMonsterException
     */
    private function checkErrorResponse(AbstractResponse $response): void
    {
        if ($response->isError()) {
            $error = ErrorResolver::resolve($response);
            throw $error ? new CapMonsterException($error) : new CapMonsterException(ErrorType::UNKNOWN_ERROR);
        }
    }
}