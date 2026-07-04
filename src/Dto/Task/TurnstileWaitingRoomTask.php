<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Task;

use CapMonsterClient\Enum\TypeTask;
use JMS\Serializer\Annotation as Serializer;

final class TurnstileWaitingRoomTask extends AbstractTask
{
    public function __construct(
        string $websiteUrl,
        string $websiteKey,
        #[Serializer\SerializedName('htmlPageBase64')]
        private readonly string $htmlPageBase64,
        ?string $userAgent = null,
        ?ProxySetting $proxySetting = null
    ) {
        parent::__construct(
            TypeTask::TURNSTILE_WAITING_ROOM_TASK,
            $websiteUrl,
            $websiteKey,
            $userAgent,
            proxySetting: $proxySetting
        );
    }

    #[Serializer\VirtualProperty(name: 'cloudflareTaskType')]
    #[Serializer\SerializedName('cloudflareTaskType')]
    public function jmsCloudflareTaskType(): string
    {
        return 'wait_room';
    }

    public function getHtmlPageBase64(): string
    {
        return $this->htmlPageBase64;
    }
}
