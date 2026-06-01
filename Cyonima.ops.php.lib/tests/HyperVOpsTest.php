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
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->createVM('TestVM', 2048, 'C:\\vhd\\disk.vhdx');

        $this->assertStringContainsString('New-VM -Name', $ops->capturedCommand);
        $this->assertStringContainsString('TestVM', $ops->capturedCommand);
        $this->assertStringContainsString('-VHDPath', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }

    public function testStartStopVMCommandsCreated(): void
    {
        $ops = new class() extends HyperVOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->startVM('TestVM');

        $this->assertStringContainsString('Start-VM -Name', $ops->capturedCommand);
        $this->assertStringContainsString('TestVM', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
