<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider\Request\Factory;

use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use CapMonsterClient\Transformer\RequestTransformer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;

final class RequestFactory implements RequestFactoryInterface
{
    public function __construct(
        private readonly SerializerBuilder $serializerBuilder,
        private readonly CapMonsterConfiguration $configuration
    ) {
    }

    public function create(AbstractRequest $request): RequestInterface
    {
        $factory = new Psr17Factory();
        $httpRequest = $factory
            ->createRequest(
                $this->configuration->getMethod(),
                implode('/', [$this->configuration->getBaseUrl(), $request->getMethod()->value])
            )->withBody(
                $factory->createStream(
                    (new RequestTransformer($this->serializerBuilder))->transform($request)
                )
            );
        foreach ($this->configuration->getHeaders() as $name => $value) {
            $httpRequest = $httpRequest->withHeader($name, $value);
        }

        return $httpRequest;
    }
}