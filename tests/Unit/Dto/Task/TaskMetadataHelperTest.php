<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Task;

use CapMonsterClient\Dto\Task\TaskMetadataHelper;
use PHPUnit\Framework\TestCase;

final class TaskMetadataHelperTest extends TestCase
{
    public function testNullReturnsBase(): void
    {
        self::assertSame(['a' => 1], TaskMetadataHelper::mergeJson(null, ['a' => 1]));
    }

    public function testEmptyStringReturnsBase(): void
    {
        self::assertSame([], TaskMetadataHelper::mergeJson('', []));
    }

    public function testValidJsonObjectIsMergedOverBase(): void
    {
        self::assertSame(
            ['a' => 1, 'b' => 2],
            TaskMetadataHelper::mergeJson('{"b":2}', ['a' => 1])
        );
    }

    public function testJsonOverridesBaseKeys(): void
    {
        self::assertSame(
            ['a' => 'json'],
            TaskMetadataHelper::mergeJson('{"a":"json"}', ['a' => 'base'])
        );
    }

    public function testInvalidJsonReturnsBase(): void
    {
        self::assertSame(['a' => 1], TaskMetadataHelper::mergeJson('{oops', ['a' => 1]));
    }

    public function testScalarJsonReturnsBase(): void
    {
        self::assertSame(['a' => 1], TaskMetadataHelper::mergeJson('"just a string"', ['a' => 1]));
    }
}
