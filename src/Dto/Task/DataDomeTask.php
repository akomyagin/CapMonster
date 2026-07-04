<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class DataDomeTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\Exclude]
        private readonly ?string $captchaUrl = null,
        #[Serializer\Exclude]
        private readonly ?string $metadata = null,
        ?string $userAgent = null,
        ?string $cookies = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            TypeTask::DATADOME_TASK,
            $websiteUrl,
            $websiteKey,
            $userAgent,
            $cookies,
            $proxySetting
        );
    }

    public function jmsSerializeTaskType(): string
    {
        return 'CustomTask';
    }

    #[Serializer\VirtualProperty(name: 'class')]
    #[Serializer\SerializedName('class')]
    public function jmsCapMonsterClass(): string
    {
        return 'DataDome';
    }

    /**
     * @return array<string, mixed>
     */
    #[Serializer\VirtualProperty(name: 'metadata')]
    #[Serializer\SerializedName('metadata')]
    public function jmsCapMonsterMetadata(): array
    {
        $meta = TaskMetadataHelper::mergeJson($this->metadata, []);
        if (($url = $this->captchaUrl) !== null && $url !== '') {
            $meta['captchaUrl'] = $url;
        }

        return $meta;
    }

    public function getCaptchaUrl(): ?string
    {
        return $this->captchaUrl;
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }
}
