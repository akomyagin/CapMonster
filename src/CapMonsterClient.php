<?php

declare(strict_types=1);

namespace CapMonsterClient;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Common\Exception\EnumResolverException;
use CapMonsterClient\Dto\Config\TimeoutConfig;
use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Enum\StatusTask;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\ApiProvider\ApiClient;
use CapMonsterClient\Resolver\TimeoutChecker;
use DateTimeImmutable;
use Psr\Http\Client\ClientInterface;

final class CapMonsterClient implements CapMonsterClientInterface
{
    private const MAX_GET_TASK_RESULT_ATTEMPTS = 120;

    private readonly ApiClient $apiProvider;

    public function __construct(
        private readonly ClientInterface $psrHttpClient,
        private readonly CapMonsterConfiguration $configuration
    ) {
        $this->apiProvider = new ApiClient($this->psrHttpClient, $this->configuration);
    }

    /**
     * @throws CapMonsterException
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
    private function getTaskSolution(AbstractTask $task): AbstractSolution
    {
        $timeout = $this->tryToGetTimeout($task->getType());
        $timeoutResolver = new TimeoutChecker();
        $startDateTime = new DateTimeImmutable();
        $currentDateTime = $timeoutResolver->resolve($timeout, $startDateTime);
        $attempts = 0;
        while (true) {
            ++$attempts;
            if ($attempts > self::MAX_GET_TASK_RESULT_ATTEMPTS) {
                throw new CapMonsterException(ErrorType::REQUEST_LIMIT_EXCEEDED);
            }
            $statusTaskResponse = $this->apiProvider->getResultTask($task);
            if ($statusTaskResponse->getStatus() === StatusTask::READY) {
                return $this->apiProvider->extractTaskSolution($task->getType(), $statusTaskResponse);
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