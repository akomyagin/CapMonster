<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\ApiProvider\Dto\Response\AbstractResponse;
use CapMonsterClient\Resolver\ErrorResolver;
use PHPUnit\Framework\TestCase;

final class ErrorResolverTest extends TestCase
{
    public function testResolveKnownError(): void
    {
        $response = $this->createResponse(1, ErrorType::NO_FUNDS->value);

        $error = ErrorResolver::resolve($response);

        $this->assertSame(ErrorType::NO_FUNDS, $error);
    }

    public function testResolveUnknownError(): void
    {
        $response = $this->createResponse(1, 'SOME_UNKNOWN_CODE');

        $error = ErrorResolver::resolve($response);

        $this->assertSame(ErrorType::UNKNOWN_ERROR, $error);
    }

    public function testResolveWithoutError(): void
    {
        $response = $this->createResponse(0, '');

        $error = ErrorResolver::resolve($response);

        $this->assertNull($error);
    }

    private function createResponse(int $errorId, string $errorCode): AbstractResponse
    {
        return new class($errorId, $errorCode) extends AbstractResponse {
            public function __construct(
                private readonly int $forcedErrorId,
                private readonly string $forcedErrorCode
            ) {
            }

            public function getErrorId(): int
            {
                return $this->forcedErrorId;
            }

            public function getErrorCode(): string
            {
                return $this->forcedErrorCode;
            }

            public function isError(): bool
            {
                return $this->forcedErrorId > 0 || $this->forcedErrorCode !== '';
            }
        };
    }
}
