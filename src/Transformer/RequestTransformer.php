<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetTaskResultRequest;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Dto\Task\AlibabaTask;
use CapMonsterClient\Dto\Task\AltchaTask;
use CapMonsterClient\Dto\Task\DataDomeTask;
use CapMonsterClient\Dto\Task\FunCaptchaTask;
use CapMonsterClient\Dto\Task\ImpervaTask;
use CapMonsterClient\Dto\Task\TSPDTask;
use CapMonsterClient\Enum\ApiMethod;
use CapMonsterClient\Enum\TypeTask;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;

/**
 * Create-task payload: {@see TypeTask} on the PHP object is not always identical to JSON `task.type`
 * (e.g. CustomTask + `class`, or {@see self::TASK_TYPE_ALIASES}).
 */
final class RequestTransformer
{
    private const TASK_TYPE_ALIASES = [
        'NoCaptchaTask' => 'RecaptchaV2Task',
        'NoCaptchaTaskProxyless' => 'RecaptchaV2TaskProxyless',
        'TurnstileChallengeTask' => 'TurnstileTask',
        'TurnstileWaitingRoomTask' => 'TurnstileTask',
    ];

    private SerializerBuilder $serializerBuilder;

    public function __construct(SerializerBuilder $serializerBuilder)
    {
        $this->serializerBuilder = $serializerBuilder;
    }

    public function transform(AbstractRequest $request): string
    {
        $serializer = $this->serializerBuilder->build();
        $arrayRequest = ['clientKey' => $request->getClientKey()];
        switch ($request->getMethod()) {
            case ApiMethod::GET_TASK_RESULT:
                /** @var GetTaskResultRequest $request */
                $arrayRequest['taskId'] = $request->getTaskId();
                break;
            case ApiMethod::CREATE_TASK:
                /** @var CreateTaskRequest $request */
                $task = $request->getTask();
                $arrayRequest['task'] = $serializer->toArray($task);
                if (isset($arrayRequest['task']['type'], self::TASK_TYPE_ALIASES[$arrayRequest['task']['type']])) {
                    $arrayRequest['task']['type'] = self::TASK_TYPE_ALIASES[$arrayRequest['task']['type']];
                }
                $arrayRequest['task'] = self::finalizeCreateTaskPayload($task, $arrayRequest['task']);
                if (!empty($request->getCallbackUrl())) {
                    $arrayRequest['callbackUrl'] = $request->getCallbackUrl();
                }
        }

        return $serializer->serialize($arrayRequest, 'json');
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private static function finalizeCreateTaskPayload(AbstractTask $task, array $payload): array
    {
        $payload = array_filter($payload, static fn (mixed $v): bool => $v !== null);
        unset($payload['taskId']);

        foreach (['websiteURL', 'websiteKey'] as $key) {
            if (($payload[$key] ?? null) === '') {
                unset($payload[$key]);
            }
        }

        if ($task instanceof FunCaptchaTask) {
            unset($payload['websiteKey'], $payload['website_public_key']);
            $payload['websitePublicKey'] = $task->getWebsitePublicKey();
        }

        if ($task instanceof DataDomeTask || $task instanceof ImpervaTask || $task instanceof AlibabaTask || $task instanceof TSPDTask) {
            unset($payload['websiteKey'], $payload['cookies']);
        }

        if ($task instanceof AltchaTask) {
            unset($payload['cookies']);
        }

        return $payload;
    }
}
