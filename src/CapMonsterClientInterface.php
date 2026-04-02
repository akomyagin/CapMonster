<?php

declare(strict_types=1);

namespace CapMonsterClient;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Dto\Task\AbstractTask;

interface CapMonsterClientInterface
{
    /**
     * @throws CapMonsterException
     */
    public function getBalance(): float;

    /**
     * @throws CapMonsterException
     */
    public function runTask(AbstractTask $task): AbstractSolution;

    /**
     * @throws CapMonsterException
     */
    public function getActualUserAgent(): string;
}