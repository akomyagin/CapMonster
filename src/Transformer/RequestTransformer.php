<?php

declare(strict_types=1);

namespace CapMonsterClient\Transformer;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetTaskResultRequest;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Enum\ApiMethod;
use CapMonsterClient\Serializer\Builder\SerializerBuilder;

final class RequestTransformer
{
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
                if (!empty($request->getCallbackUrl())) {
                    $arrayRequest['callbackUrl'] = $request->getCallbackUrl();
                }
        }

        return $serializer->serialize($arrayRequest, 'json');
    }
}