<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Linux\LinuxMintOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class LinuxMintOpsTest extends TestCase
{
    public function testInstallPackageUsesApt(): void
    {
        $ops = new class() extends LinuxMintOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->installPackage('htop');

        $this->assertStringContainsString('apt install -y ', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
