<?php

declare(strict_types=1);

namespace Tests\Unit;

use CapMonsterClient\CapMonsterConfiguration;

final class CapMonsterConfigurationHeadersTest extends AbstractTestCase
{
    public function testHasJsonHeadersByDefault(): void
    {
        $configuration = new CapMonsterConfiguration(self::SECRET_KEY);

        $this->assertSame('application/json', $configuration->getHeaders()['Content-Type']);
        $this->assertSame('application/json', $configuration->getHeaders()['Accept']);
    }
}
