<?php

declare(strict_types=1);

namespace Tests\Unit\Serializer;

use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Serializer\TaskDiscriminatorRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Regression guard: the registry once referenced a non-existent enum case
 * (TURNSTILE_CHALLENGE_TASK_PROXYLESS), which made the whole class fatal on load.
 * The map must stay exhaustive and every entry must be a real, loadable task class.
 */
final class TaskDiscriminatorRegistryTest extends TestCase
{
    public function testRegistryClassIsLoadable(): void
    {
        self::assertTrue(class_exists(TaskDiscriminatorRegistry::class));
        self::assertIsArray(TaskDiscriminatorRegistry::TYPE_TO_CLASS);
    }

    public function testEveryTypeTaskCaseHasARegistryEntry(): void
    {
        foreach (TypeTask::cases() as $case) {
            self::assertArrayHasKey(
                $case->value,
                TaskDiscriminatorRegistry::TYPE_TO_CLASS,
                sprintf('TypeTask::%s (%s) has no registry entry', $case->name, $case->value)
            );
        }
    }

    public function testRegistryHasNoStaleEntriesBeyondTheEnum(): void
    {
        self::assertCount(count(TypeTask::cases()), TaskDiscriminatorRegistry::TYPE_TO_CLASS);
    }

    public function testEveryRegistryEntryIsALoadableTaskClass(): void
    {
        foreach (TaskDiscriminatorRegistry::TYPE_TO_CLASS as $type => $class) {
            self::assertTrue(class_exists($class), sprintf('%s maps to missing class %s', $type, $class));
            self::assertTrue(
                is_subclass_of($class, AbstractTask::class),
                sprintf('%s maps to %s which is not an AbstractTask', $type, $class)
            );
        }
    }
}
