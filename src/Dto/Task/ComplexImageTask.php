<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class ComplexImageTask extends AbstractTask
{
    /**
     * @param list<string> $imagesBase64 base64-encoded image file contents (no data: URI prefix, no line breaks)
     * @param string $task the sub-recognizer identifier, e.g. "baidu", "dli" (goes into metadata.Task)
     */
    public function __construct(
        #[Serializer\SerializedName('imagesBase64')]
        private readonly array $imagesBase64,
        #[Serializer\Exclude]
        private readonly string $task,
        #[Serializer\Exclude]
        private readonly ?string $metadata = null
    ) {
        parent::__construct(TypeTask::COMPLEX_IMAGE_TASK, '', '');
    }

    #[Serializer\VirtualProperty(name: 'class')]
    #[Serializer\SerializedName('class')]
    public function jmsCapMonsterClass(): string
    {
        return 'recognition';
    }

    /**
     * @return array<string, mixed>
     */
    #[Serializer\VirtualProperty(name: 'metadata')]
    #[Serializer\SerializedName('metadata')]
    public function jmsCapMonsterMetadata(): array
    {
        return TaskMetadataHelper::mergeJson($this->metadata, ['Task' => $this->task]);
    }

    /**
     * @return list<string>
     */
    public function getImagesBase64(): array
    {
        return $this->imagesBase64;
    }

    public function getTask(): string
    {
        return $this->task;
    }

    public function getMetadata(): ?string
    {
        return $this->metadata;
    }
}
