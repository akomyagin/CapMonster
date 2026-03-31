<?php

declare(strict_types=1);

namespace Tests\FakeHttpClient;

use Psr\Http\Server\RequestHandlerInterface;
use Webclient\Fake\Handler\SpecHandler\SpecHandlerBuilder;

final class HandlerBuilder
{
    public static function build(callable $ruleFunction, callable $responseFunction): RequestHandlerInterface
    {
        $builder = SpecHandlerBuilder::create();

        $builder
            ->route($ruleFunction)
            ->response($responseFunction);

        return $builder->build();
    }
}