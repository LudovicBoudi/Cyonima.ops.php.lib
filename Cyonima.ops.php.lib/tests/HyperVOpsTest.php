<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Windows\HyperVOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class HyperVOpsTest extends TestCase
{
    public function testCreateVMBuildsNewVM(): void
    {
        $ops = new class() extends HyperVOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('New-VM -Name ' . self::escapePowerShellArgument('TestVM'), $command);
                $this->assertStringContainsString('-VHDPath ' . self::escapePowerShellArgument('C:\\vhd\\disk.vhdx'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->createVM('TestVM', 2048, 'C:\\vhd\\disk.vhdx');
        $this->assertSame(0, $result->getExitCode());
    }

    public function testStartStopVMCommandsCreated(): void
    {
        $ops = new class() extends HyperVOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('Start-VM -Name ' . self::escapePowerShellArgument('TestVM'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->startVM('TestVM');
        $this->assertSame(0, $result->getExitCode());
    }
}
