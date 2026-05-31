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
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('apt install -y ' . self::escapeShellArgument('htop'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->installPackage('htop');
        $this->assertSame(0, $result->getExitCode());
    }
}
