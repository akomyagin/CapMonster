<?php

namespace CapMonsterClient\ApiProvider\Request\Factory;

use CapMonsterClient\Common\Dto\Request\AbstractRequest;
use Psr\Http\Message\RequestInterface;

interface RequestFactoryInterface
{
    public function create(AbstractRequest $request): RequestInterface;
}