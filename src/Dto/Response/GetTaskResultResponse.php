<?php

declare(strict_types=1);

namespace CapMonsterClient\Dto\Response;

use CapMonsterClient\Dto\Solution\AbstractSolution;
use CapMonsterClient\Enum\StatusTask;

final class GetTaskResultResponse extends AbstractResponse
{
    public function __construct(
        private readonly StatusTask       $status,
        private readonly AbstractSolution $solution,
                                          $errorId,
                                          $errorCode
    ) {
        parent::__construct($errorId, $errorCode);
    }

    public function getStatus(): StatusTask
    {
        return $this->status;
    }

    public function getSolution(): AbstractSolution
    {
        return $this->solution;
    }
}