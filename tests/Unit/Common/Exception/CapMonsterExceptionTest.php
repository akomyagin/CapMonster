<?php

declare(strict_types=1);

namespace Tests\Unit\Common\Exception;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Common\Exception\ExceptionFactory;
use CapMonsterClient\Enum\ErrorType;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;

final class CapMonsterExceptionTest extends TestCase
{
    public function testMessageComesFromErrorTypeDescription(): void
    {
        $exception = new CapMonsterException(ErrorType::NO_FUNDS);

        self::assertSame(ErrorType::NO_FUNDS, $exception->getType());
        self::assertSame(ErrorType::NO_FUNDS->description(), $exception->getMessage());
        self::assertSame(0, $exception->getCode());
        self::assertNull($exception->getPrevious());
    }

    public function testImplementsPsrClientExceptionInterface(): void
    {
        self::assertInstanceOf(ClientExceptionInterface::class, new CapMonsterException(ErrorType::UNKNOWN_ERROR));
    }

    public function testPreviousExceptionCodeAndChainArePreserved(): void
    {
        $previous = new Exception('boom', 503);
        $exception = new CapMonsterException(ErrorType::RESPONSE_CODE_ERROR, $previous);

        self::assertSame(503, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }

    public function testFactoryProducesEquivalentException(): void
    {
        $previous = new Exception('cause', 42);
        $exception = ExceptionFactory::fromErrorType(ErrorType::SEND_MESSAGE_ERROR, $previous);

        self::assertInstanceOf(CapMonsterException::class, $exception);
        self::assertSame(ErrorType::SEND_MESSAGE_ERROR, $exception->getType());
        self::assertSame(ErrorType::SEND_MESSAGE_ERROR->description(), $exception->getMessage());
        self::assertSame(42, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }

    /**
     * @return iterable<string, array{ErrorType}>
     */
    public static function provideAllErrorTypes(): iterable
    {
        foreach (ErrorType::cases() as $case) {
            yield $case->name => [$case];
        }
    }

    #[DataProvider('provideAllErrorTypes')]
    public function testEveryErrorTypeHasANonEmptyDescription(ErrorType $type): void
    {
        self::assertNotSame('', trim($type->description()));
        self::assertSame($type->description(), (new CapMonsterException($type))->getMessage());
    }
}
