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
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $ops->installPackage('htop');

        $this->assertSame("pkg install -y 'htop'", $ops->capturedCommand);
    }

    public function testManageServiceStartUsesServiceCommand(): void
    {
        $ops = new class() extends FreeBsdOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $ops->manageService('sshd', 'start');

        $this->assertStringContainsString("service", $ops->capturedCommand);
        $this->assertStringContainsString("'sshd'", $ops->capturedCommand);
        $this->assertStringContainsString("'start'", $ops->capturedCommand);
    }

    public function testGetDistributionReturnsFreeBSD(): void
    {
        $ops = new class() extends FreeBsdOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $this->assertSame('FreeBSD', $ops->getDistribution());
    }
}
