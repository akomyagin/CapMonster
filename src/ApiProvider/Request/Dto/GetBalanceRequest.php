<?php

declare(strict_types = 1);

namespace CapMonsterClient\ApiProvider\Request\Dto;

use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use CapMonsterClient\Enum\ApiMethod;

final class GetBalanceRequest extends AbstractRequest
{
    public function getMethod(): ApiMethod
    {
        return ApiMethod::GET_BALANCE;
    }
}