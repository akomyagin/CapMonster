<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider;

use CapMonsterClient\ApiProvider\Dto\Response\AbstractResponse;
use CapMonsterClient\ApiProvider\Dto\Response\CreateTaskResponse;
use CapMonsterClient\ApiProvider\Dto\Response\GetBalanceResponse;
use CapMonsterClient\ApiProvider\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetBalanceRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetTaskResultRequest;
use CapMonsterClient\ApiProvider\Request\Factory\RequestFactory;
use CapMonsterClient\ApiProvider\Request\Factory\RequestFactoryInterface;
use CapMonsterClient\ApiProvider\Transformer\FromJsonTransformer;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\ExceptionFactory;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Dto\Solution\RawSolution;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Resolver\ErrorResolver;
use CapMonsterClient\Resolver\TypeSolutionResolver;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use Exception;
use JMS\Serializer\Exception\RuntimeException;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiClient
{
    private readonly SerializerBuilder $serializerBuilder;

    private readonly RequestFactoryInterface $requestFactory;

    public function __construct(
        private readonly ClientInterface $psrHttpClient,
        private readonly CapMonsterConfiguration $configuration,
        ?SerializerBuilder $serializerBuilder = null
    ) {
        $this->serializerBuilder = $serializerBuilder ?? new SerializerBuilder();
        $this->requestFactory = new RequestFactory($this->serializerBuilder, $this->configuration);
    }

    /**
     * @throws CapMonsterException
     */
    public function getBalance(): float
    {
        $request = new GetBalanceRequest($this->configuration->getClientKey());
        $response = $this->sendRequest($request);
        $dtoResponse = $this->processResponse($response, GetBalanceResponse::class);

        return $dtoResponse->getBalance();
    }

    /**
     * @throws CapMonsterException
     */
    public function createTask(AbstractTask $task): CreateTaskResponse
    {
        $request = new CreateTaskRequest(
            $this->configuration->getClientKey(),
            $task,
            $this->configuration->getCallbackUrl()
        );
        $response = $this->sendRequest($request);

        return $this->processResponse($response, CreateTaskResponse::class);
    }

    /**
     * @throws CapMonsterException
     */
    public function getResultTask(AbstractTask $task): GetTaskResultResponse
    {
        $request = new GetTaskResultRequest(
            $this->configuration->getClientKey(),
            $task
        );
        $response = $this->sendRequest($request);

        return $this->processResponse($response, GetTaskResultResponse::class);
    }

    /**
     * @throws CapMonsterException
     */
    public function extractTaskSolution(TypeTask $typeTask, GetTaskResultResponse $response): AbstractSolution
    {
        $className = (new TypeSolutionResolver())->resolve($typeTask);
        if ($className === RawSolution::class) {
            return new RawSolution($response->getSolution());
        }
        try {
            return $this->serializerBuilder->build()->fromArray($response->getSolution(), $className);
        } catch (RuntimeException $exception) {
            throw new CapMonsterException(ErrorType::RESPONSE_ERROR, $exception);
        }
    }

    /**
     * Returns the currently recommended browser User-Agent from CapMonster.
     *
     * @throws CapMonsterException
     */
    public function getActualUserAgent(): string
    {
        $request = new Request('GET', 'https://capmonster.cloud/api/useragent/actual');
        try {
            $response = $this->psrHttpClient->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw ExceptionFactory::fromErrorType(ErrorType::SEND_MESSAGE_ERROR, $exception);
        }
        $this->assertSuccessfulResponse($response);
        $userAgent = trim((string) $response->getBody());
        if ($userAgent === '') {
            throw ExceptionFactory::fromErrorType(ErrorType::RESPONSE_ERROR);
        }

        return $userAgent;
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
            throw ExceptionFactory::fromErrorType(ErrorType::SEND_MESSAGE_ERROR, $exception);
        }
    }

    /**
     * @throws CapMonsterException
     */
    private function assertSuccessfulResponse(ResponseInterface $response): void
    {
        $code = $response->getStatusCode();
        if (!preg_match('~^2\d+~', (string) $code)) {
            throw ExceptionFactory::fromErrorType(
                ErrorType::RESPONSE_CODE_ERROR,
                new Exception((string) $response->getBody(), $code)
            );
        }
    }

    /**
     * @throws CapMonsterException
     */
    private function checkErrorResponse(AbstractResponse $response): void
    {
        if ($response->isError()) {
            $error = ErrorResolver::resolve($response);
            throw ExceptionFactory::fromErrorType($error ?? ErrorType::UNKNOWN_ERROR);
        }
    }

    /**
     * Parses a JSON API response and maps it to a DTO or a CapMonsterException.
     *
     * The body is inspected for a structured error BEFORE the HTTP status code is
     * considered: the real API returns auth errors (e.g. ERROR_KEY_DOES_NOT_EXIST)
     * as HTTP 403 with the same JSON error shape it uses for HTTP 200 errors, and
     * those must map to their specific ErrorType instead of a generic
     * RESPONSE_CODE_ERROR. The generic RESPONSE_CODE_ERROR (with the original HTTP
     * status preserved as the exception code) remains the fallback for non-2xx
     * responses whose body carries no parseable structured error.
     *
     * @template T of AbstractResponse
     *
     * @param class-string<T> $className
     *
     * @return T
     *
     * @throws CapMonsterException
     */
    private function processResponse(ResponseInterface $response, string $className): AbstractResponse
    {
        $body = (string) $response->getBody();
        $code = $response->getStatusCode();
        $isSuccessStatusCode = (bool) preg_match('~^2\d+~', (string) $code);

        try {
            /** @var T $dtoResponse */
            $dtoResponse = (new FromJsonTransformer($this->serializerBuilder, $className))->transform($body);
        } catch (RuntimeException $exception) {
            if ($isSuccessStatusCode) {
                throw ExceptionFactory::fromErrorType(ErrorType::RESPONSE_ERROR, $exception);
            }
            throw ExceptionFactory::fromErrorType(
                ErrorType::RESPONSE_CODE_ERROR,
                new Exception($body, $code)
            );
        }

        $this->checkErrorResponse($dtoResponse);

        if (!$isSuccessStatusCode) {
            throw ExceptionFactory::fromErrorType(
                ErrorType::RESPONSE_CODE_ERROR,
                new Exception($body, $code)
            );
        }

        return $dtoResponse;
    }
}
