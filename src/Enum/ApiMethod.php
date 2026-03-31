<?php

declare(strict_types=1);

namespace CapMonsterClient\Enum;

enum ApiMethod: string
{
    case CREATE_TASK = 'createTask';

    case GET_TASK_RESULT = 'getTaskResult';

    case GET_BALANCE = 'getBalance';
}