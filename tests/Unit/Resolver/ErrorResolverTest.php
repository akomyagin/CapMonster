<?php

declare(strict_types=1);

namespace Tests\Unit\Resolver;

use CapMonsterClient\ApiProvider\Dto\Response\GetBalanceResponse;
use CapMonsterClient\Enum\ErrorType;
use CapMonsterClient\Resolver\ErrorResolver;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ErrorResolverTest extends TestCase
{
    /**
     * @param array<string, mixed> $data
     */
    private function response(array $data): GetBalanceResponse
    {
        $response = (new SerializerBuilder())->build()->fromArray($data, GetBalanceResponse::class);
        assert($response instanceof GetBalanceResponse);

        return $response;
    }

    public function testReturnsNullForSuccessfulResponse(): void
    {
        self::assertNull(ErrorResolver::resolve($this->response(['errorId' => 0, 'balance' => 1.0])));
    }

    public function testResolvesKnownErrorCode(): void
    {
        $response = $this->response(['errorId' => 1, 'errorCode' => 'ERROR_ZERO_BALANCE', 'balance' => 0.0]);

        self::assertSame(ErrorType::NO_FUNDS, ErrorResolver::resolve($response));
    }

    public function testUnknownErrorCodeFallsBackToUnknownError(): void
    {
        $response = $this->response(['errorId' => 1, 'errorCode' => 'ERROR_FROM_THE_FUTURE', 'balance' => 0.0]);

        self::assertSame(ErrorType::UNKNOWN_ERROR, ErrorResolver::resolve($response));
    }

    public function testErrorIdWithoutCodeFallsBackToUnknownError(): void
    {
        $response = $this->response(['errorId' => 7, 'balance' => 0.0]);

        self::assertSame(ErrorType::UNKNOWN_ERROR, ErrorResolver::resolve($response));
    }

    public function testErrorCodeWithZeroErrorIdIsStillAnError(): void
    {
        $response = $this->response(['errorId' => 0, 'errorCode' => 'ERROR_IP_BANNED', 'balance' => 0.0]);

        self::assertTrue($response->isError());
        self::assertSame(ErrorType::IP_BANNED, ErrorResolver::resolve($response));
    }

    /**
     * @return iterable<string, array{string, ErrorType}>
     */
    public static function provideAllWireErrorCodes(): iterable
    {
        foreach (ErrorType::cases() as $case) {
            yield $case->name => [$case->value, $case];
        }
    }

    #[DataProvider('provideAllWireErrorCodes')]
    public function testEveryErrorTypeWireCodeResolvesToItsCase(string $code, ErrorType $expected): void
    {
        $response = $this->response(['errorId' => 1, 'errorCode' => $code, 'balance' => 0.0]);

        self::assertSame($expected, ErrorResolver::resolve($response));
    }
}
