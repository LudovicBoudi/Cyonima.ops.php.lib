<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Bsd\FreeBsdOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class FreeBsdOpsTest extends TestCase
{
    public function testInstallPackageUsesPkgInstall(): void
    {
        $ops = new class() extends FreeBsdOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertSame("pkg install -y 'htop'", $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $ops->installPackage('htop');
    }

    public function testManageServiceStartUsesServiceCommand(): void
    {
        $ops = new class() extends FreeBsdOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertSame("service 'sshd' start", $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $ops->manageService('sshd', 'start');
    }

    public function testGetDistributionReturnsFreeBSD(): void
    {
        $ops = new class() extends FreeBsdOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $this->assertSame('FreeBSD', $ops->getDistribution());
    }
}
