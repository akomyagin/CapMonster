<?php

declare(strict_types=1);

namespace Tests\Unit;

use CapMonsterClient\CapMonsterConfiguration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Tests\FakeHttpClient\HttpClientHandlerFactory;
use Webclient\Fake\FakeHttpClient;

abstract class AbstractTestCase extends TestCase
{
    public const SECRET_KEY = 'secretKey';

    protected CapMonsterConfiguration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new CapMonsterConfiguration(self::SECRET_KEY);
    }

    protected function createHttpClient(array $fieldValue = []): ClientInterface
    {
        $factory = new HttpClientHandlerFactory($this->configuration->getBaseUrl(), $fieldValue);

        return new FakeHttpClient($factory->create());
    }
}