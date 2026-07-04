<?php

declare(strict_types=1);

namespace CapMonsterClient\Serializer\Builder;

use CapMonsterClient\Serializer\TaskDiscriminatorRegistry;
use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder as JMSBuilder;
use JMS\Serializer\SerializerInterface;

/**
 * JMS is configured with enum support. Polymorphic task type ↔ class mapping lives in
 * {@see TaskDiscriminatorRegistry::TYPE_TO_CLASS} (JMS #[Discriminator] is not used on {@see \CapMonsterClient\Dto\Task\AbstractTask}
 * because several {@see \CapMonsterClient\Enum\TypeTask} values share one PHP class and CustomTask uses a different wire `type`).
 */
final class SerializerBuilder
{
    private ?Serializer $serializer = null;

    public function build(): SerializerInterface&ArrayTransformerInterface
    {
        if ($this->serializer === null) {
            $serializer = JMSBuilder::create();
            $serializer->enableEnumSupport();
            $this->serializer = $serializer->build();
        }

        return $this->serializer;
    }
}
