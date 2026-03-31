<?php

declare(strict_types=1);

namespace CapMonsterClient\Serializer\Builder;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder as JMSBuilder;
use JMS\Serializer\SerializerInterface;

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
