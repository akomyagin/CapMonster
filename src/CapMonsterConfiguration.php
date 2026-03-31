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

    private const DEFAULT_TIMEOUTS = [
        [
            'taskType' => TypeTask::FUN_CAPTCHA_TASK,
            'firstRequestDelay' => 1,
            'requestInterval' => 1,
            'timeout' => 10,
        ],
        [
            'taskType' => TypeTask::FUN_CAPTCHA_TASK_PROXYLESS,
            'firstRequestDelay' => 1,
            'requestInterval' => 1,
            'timeout' => 10,
        ],
        [
            'taskType' => TypeTask::NO_CAPTCHA_TASK,
            'firstRequestDelay' => 1,
            'requestInterval' => 1,
            'timeout' => 10,
        ],
    ];

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
        foreach (self::DEFAULT_TIMEOUTS as $timeout) {
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
}