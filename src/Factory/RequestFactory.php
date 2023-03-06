<?php

declare(strict_types=1);

namespace CapMonsterClient\Factory;

use CapMonsterClient\CapMonsterConfiguration;
use CapMonsterClient\Dto\Task\AbstractTask;
use Psr\Http\Message\RequestInterface;

final class RequestFactory
{
    public function __construct(
        private readonly CapMonsterConfiguration $configuration
    ) {
    }

    public function create(AbstractTask $task): RequestInterface
    {

    }
}