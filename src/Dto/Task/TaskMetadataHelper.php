<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

/**
 * @internal
 */
final class TaskMetadataHelper
{
    /**
     * @param array<string, mixed> $base
     *
     * @return array<string, mixed>
     */
    public static function mergeJson(?string $metadataJson, array $base = []): array
    {
        if ($metadataJson === null || $metadataJson === '') {
            return $base;
        }
        $decoded = json_decode($metadataJson, true);

        return is_array($decoded) ? array_merge($base, $decoded) : $base;
    }
}
