<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider;

use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\External\Dto\Response\CreateTaskResponse;
use CapMonsterClient\External\Dto\Response\GetTaskResultResponse;
use CapMonsterClient\External\ExternalApiProvider;
use Psr\Http\Client\ClientInterface;

/**
 * @deprecated Use ExternalApiProvider directly.
 */
final class ApiProviderService
{
    private readonly ExternalApiProvider $provider;

    public function __construct(
        private readonly ClientInterface $psrHttpClient,
        private readonly CapMonsterConfiguration $configuration
    ) {
        $this->provider = new ExternalApiProvider($this->psrHttpClient, $this->configuration);
    }

    /**
     * @throws CapMonsterException
     */
    public function getBalance(): float
    {
        return $this->provider->getBalance();
    }

    /**
     * @throws CapMonsterException
     */
    public function createTask(AbstractTask $task): CreateTaskResponse
    {
        return $this->provider->createTask($task);
    }

    /**
     * @throws CapMonsterException
     */
    public function getResultTask(?AbstractTask $task = null): GetTaskResultResponse
    {
        if ($task === null) {
            throw new \BadMethodCallException('Use getResultTask(AbstractTask $task).');
        }

        return $this->provider->getResultTask($task);
    }
}