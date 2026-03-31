<?php

declare(strict_types=1);

namespace CapMonsterClient;

use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Dto\Task\AbstractTask;
use Psr\Http\Client\ClientExceptionInterface;

interface CapMonsterClientInterface
{
    /**
     * @throws ClientExceptionInterface
     */
    public function getBalance(): float;

    /**
     * @throws ClientExceptionInterface
     */
    public function runTask(AbstractTask $task): AbstractSolution;
}