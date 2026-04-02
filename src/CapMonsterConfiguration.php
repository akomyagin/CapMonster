<?php

declare(strict_types=1);

namespace CapMonsterClient;

use CapMonsterClient\Common\Exception\EnumResolverException;
use CapMonsterClient\Dto\Config\Config;
use CapMonsterClient\Dto\Config\TimeoutConfig;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use CapMonsterClient\Transformer\FromArrayTransformer;

final class CapMonsterConfiguration
{
    private const DEFAULT_CONFIG = [
        'method' => 'POST',
        'callbackUrl' => null,
        'baseUrl' => 'https://api.capmonster.cloud',
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ];

    private const DEFAULT_FIRST_REQUEST_DELAY = 2;
    private const DEFAULT_REQUEST_INTERVAL = 2;
    private const DEFAULT_TIMEOUT = 120;

    private Config $config;

    public function __construct(
        private readonly string $clientKey,
        array $config = []
    ) {
        $this->config = (new FromArrayTransformer())->transform(Config::class, $this->mergeConfig($config));
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->config->getCallbackUrl();
    }

    public function getBaseUrl(): string
    {
        return $this->config->getBaseUrl();
    }

    public function getMethod(): string
    {
        return $this->config->getMethod();
    }

    public function getHeaders(): array
    {
        return $this->config->getHeaders();
    }

    /**
     * @throws EnumResolverException
     */
    public function getTimeoutConfig(TypeTask $typeTask): TimeoutConfig
    {
        foreach ($this->config->getTimeouts() as $timeout) {
            if ($timeout->getTypeTask() === $typeTask) {
                return $timeout;
            }
        }

        throw new EnumResolverException(sprintf('Timeout not found for task %s', $typeTask->name));
    }

    private function mergeConfig(array $config): array
    {
        $timeouts = $this->mergeTimeouts($config['timeouts'] ?? []);
        unset($config['timeouts']);
        $config = array_merge(self::DEFAULT_CONFIG, $config);
        $config['timeouts'] = $timeouts;
        foreach ($config['timeouts'] as &$timeout) {
            if ($timeout['taskType'] instanceof TypeTask) {
                $timeout['taskType'] = $timeout['taskType']->value;
            }
        }

        return $config;
    }

    private function mergeTimeouts(array $customTimeouts): array
    {
        $timeouts = [];
        foreach (self::buildDefaultTimeouts() as $timeout) {
            $timeouts[$timeout['taskType']->value] = $timeout;
        }
        foreach ($customTimeouts as $timeout) {
            $taskType = $timeout['taskType'] instanceof TypeTask
                ? $timeout['taskType']->value
                : (string) $timeout['taskType'];
            $timeouts[$taskType] = $timeout;
        }

        return array_values($timeouts);
    }

    /**
     * @return array<int, array{taskType: TypeTask, firstRequestDelay: int, requestInterval: int, timeout: int}>
     */
    private static function buildDefaultTimeouts(): array
    {
        $timeouts = [];
        foreach (TypeTask::cases() as $typeTask) {
            $timeouts[] = [
                'taskType' => $typeTask,
                'firstRequestDelay' => self::DEFAULT_FIRST_REQUEST_DELAY,
                'requestInterval' => self::DEFAULT_REQUEST_INTERVAL,
                'timeout' => self::DEFAULT_TIMEOUT,
            ];
        }

        return $timeouts;
    }
}