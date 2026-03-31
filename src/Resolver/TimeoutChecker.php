<?php

declare(strict_types = 1);

namespace CapMonsterClient\Resolver;

use CapMonsterClient\Common\Exception\CapMonsterException;
use CapMonsterClient\Dto\Config\TimeoutConfig;
use CapMonsterClient\Enum\ErrorType;
use DateTimeImmutable;

final class TimeoutChecker
{
    /**
     * @throws CapMonsterException
     */
    public function resolve(
        TimeoutConfig $timeoutConfig,
        DateTimeImmutable $startDateTime,
        DateTimeImmutable $currentDateTime = null
    ): DateTimeImmutable {
        if ($currentDateTime === null) {
            sleep($timeoutConfig->getFirstRequestDelay());
            return $startDateTime->modify(sprintf('+%s secomd', $timeoutConfig->getFirstRequestDelay()));
        }
        $currentDateTime = $this->modify($currentDateTime, $timeoutConfig->getRequestInterval());
        if ($currentDateTime > $this->modify($startDateTime, $timeoutConfig->getTimeout())) {
            throw new CapMonsterException(ErrorType::TIMEOUT_EXPIRED);
        }
        sleep($timeoutConfig->getRequestInterval());

        return $currentDateTime;
    }

    private function modify(DateTimeImmutable $dateTime, int $seconds): DateTimeImmutable
    {
        return $dateTime->modify(sprintf('+%s seconds', $seconds));
    }
}