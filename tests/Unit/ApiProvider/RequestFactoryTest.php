<?php

declare(strict_types=1);

namespace Tests\Unit\ApiProvider;

use CapMonsterClient\ApiProvider\Request\Dto\GetBalanceRequest;
use CapMonsterClient\ApiProvider\Request\Factory\RequestFactory;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use PHPUnit\Framework\TestCase;

final class RequestFactoryTest extends TestCase
{
    public function testBuildsUriForGetBalanceMethod(): void
    {
        $configuration = new CapMonsterConfiguration(
            'my-client-key',
            ['baseUrl' => 'https://api.capmonster.cloud']
        );
        $factory = new RequestFactory(new SerializerBuilder(), $configuration);
        $request = $factory->create(new GetBalanceRequest('my-client-key'));

        $this->assertSame(
            'https://api.capmonster.cloud/getBalance',
            (string) $request->getUri()
        );
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
    }
}
