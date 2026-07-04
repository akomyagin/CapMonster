<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class TenDITask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\Exclude]
        private readonly ?string $metadata = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(TypeTask::TENDI_TASK, $websiteUrl, $websiteKey, $userAgent, $cookies, $proxySetting);
    }

    public function jmsSerializeTaskType(): string
    {
        return 'CustomTask';
    }

    #[Serializer\VirtualProperty(name: 'class')]
    #[Serializer\SerializedName('class')]
    public function jmsCapMonsterClass(): string
    {
        return 'TenDI';
    }

    /**
     * @return array<string, mixed>
     */
    #[Serializer\VirtualProperty(name: 'metadata')]
    #[Serializer\SerializedName('metadata')]
    public function jmsCapMonsterMetadata(): array
    {
        return TaskMetadataHelper::mergeJson($this->metadata, []);
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }
}
