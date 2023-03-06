<?php

declare(strict_types=1);

namespace CapMonsterClient;

use CapMonsterClient\Dto\Response\AbstractResponse;
use CapMonsterClient\Dto\Task\AbstractTask;
use CapMonsterClient\Factory\RequestFactory;
use Psr\Http\Client\ClientInterface;

final class CapMonsterClient
{
    public function __construct(
        private readonly ClientInterface $psrHttpClient,
        private readonly CapMonsterConfiguration $configuration
    ) {
    }


    public function runTask(AbstractTask $task): AbstractResponse
    {
        $factory = new RequestFactory($this->configuration);
        $response = $this->psrHttpClient->
    }
}