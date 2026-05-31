<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Linux\ZorinOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class ZorinOpsTest extends TestCase
{
    public function testUpgradePackagesInvokesAptUpdateUpgrade(): void
    {
        $ops = new class() extends ZorinOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('apt update && apt upgrade -y', $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->upgradePackages();
        $this->assertSame(0, $result->getExitCode());
    }
}
