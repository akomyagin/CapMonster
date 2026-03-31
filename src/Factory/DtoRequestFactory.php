<?php

declare(strict_types=1);

namespace CapMonsterClient\Factory;

use CapMonsterClient\ApiProvider\Request\Dto\CreateTaskRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetBalanceRequest;
use CapMonsterClient\ApiProvider\Request\Dto\GetTaskResultRequest;
use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Enum\ApiMethod;
use CapMonsterClient\Enum\ErrorType;

final class DtoRequestFactory
{
    /**
     * @throws CapMonsterException
     */
    public function createDtoRequest(
        ApiMethod $apiMethod,
        CapMonsterConfiguration  $configuration,
        AbstractTask $task = null
    ): AbstractRequest {
        switch ($apiMethod) {
            case ApiMethod::CREATE_TASK:
                return
                    new CreateTaskRequest(
                        $configuration->getClientKey(),
                        $task,
                        $configuration->getCallbackUrl()
                    );
            case ApiMethod::GET_BALANCE:
                return
                    new GetBalanceRequest(
                        $configuration->getClientKey()
                    );
            case ApiMethod::GET_TASK_RESULT:
                return
                    new GetTaskResultRequest(
                        $configuration->getClientKey(),
                        $task
                    );
        }

        throw new CapMonsterException(ErrorType::INVALID_ARGUMENT_EXCEPTION);
    }
}