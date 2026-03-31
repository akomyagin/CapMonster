<?php

declare(strict_types=1);

namespace CapMonsterClient\ApiProvider\Request\Factory;

use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use CapMonsterClient\Transformer\RequestTransformer;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Stream;
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
        $fp = fopen("php://temp", 'wb+');
        fwrite($fp, (new RequestTransformer($this->serializerBuilder))->transform($request));

        return
            new Request(
                implode('/', [$this->configuration->getBaseUrl(), $request->getMethod()->value]),
                $this->configuration->getMethod(),
                new Stream($fp),
                $this->configuration->getHeaders()
            );
    }
}