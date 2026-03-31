<?php

declare(strict_types=1);

namespace Tests\FakeHttpClient;

use Psr\Http\Message\ResponseInterface;
use Webclient\Fake\Handler\SpecHandler\Rule;

interface HandlerFactoryInterface
{
    public function setRouteRule(Rule $rule): void;

    public function getResponse(ResponseInterface $response): ResponseInterface;
}