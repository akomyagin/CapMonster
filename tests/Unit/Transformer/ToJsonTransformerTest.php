<?php

declare(strict_types=1);

namespace Tests\Unit\Transformer;

use CapMonsterClient\Transformer\ToJsonTransformer;
use PHPUnit\Framework\TestCase;

final class ToJsonTransformerTest extends TestCase
{
    public function testTransformObjectToJson(): void
    {
        $transformer = new ToJsonTransformer();
        $payload = new class {
            public string $foo = 'bar';
        };

        $json = $transformer->transform($payload);

        $this->assertSame('{"foo":"bar"}', $json);
    }
}
