<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\ApiProvider\Dto\Response\AbstractResponse;
use CapMonsterClient\Resolver\ErrorResolver;
use PHPUnit\Framework\TestCase;

final class ErrorResolverTest extends TestCase
{
    /**
     * @dataProvider provideKnownErrors
     */
    public function testResolveKnownError(string $errorCode, ErrorType $expectedError): void
    {
        $response = $this->createResponse(1, $errorCode);

        $error = ErrorResolver::resolve($response);

        $this->assertSame($expectedError, $error);
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

    /**
     * @return array<string, array{0: string, 1: ErrorType}>
     */
    public static function provideKnownErrors(): array
    {
        return [
            'no_funds' => [ErrorType::NO_FUNDS->value, ErrorType::NO_FUNDS],
            'proxy_missing' => [ErrorType::PROXY_MISSING->value, ErrorType::PROXY_MISSING],
            'proxy_not_authorised' => [ErrorType::PROXY_NOT_AUTHORISED->value, ErrorType::PROXY_NOT_AUTHORISED],
            'proxy_read_timeout' => [ErrorType::PROXY_READ_TIMEOUT->value, ErrorType::PROXY_READ_TIMEOUT],
            'task_absent' => [ErrorType::TASK_ABSENT->value, ErrorType::TASK_ABSENT],
            'wrong_useragent' => [ErrorType::WRONG_USERAGENT->value, ErrorType::WRONG_USERAGENT],
        ];
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
