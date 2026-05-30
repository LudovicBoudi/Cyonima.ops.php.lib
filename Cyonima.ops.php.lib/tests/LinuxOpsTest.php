<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Linux\UbuntuOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class LinuxOpsTest extends TestCase
{
    public function testReadFileUsesCatWithArgument(): void
    {
        $path = '/tmp/example.log';
        $ops = new class() extends UbuntuOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertSame('cat ' . self::escapeShellArgument('/tmp/example.log'), $command);
                return new RemoteCommandOutput('line1', '', 0);
            }
        };

        $result = $ops->readFile($path);

        $this->assertSame('line1', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testWriteFileEncodesContentAndWritesToDestination(): void
    {
        $path = '/tmp/out.txt';
        $content = "hello world\n";
        $expected = base64_encode($content);

        $ops = new class() extends UbuntuOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('echo ' . self::escapeShellArgument('' . base64_encode("hello world\n") . ''), $command);
                $this->assertStringContainsString('base64 --decode > ' . self::escapeShellArgument('/tmp/out.txt'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->writeFile($path, $content);

        $this->assertSame(0, $result->getExitCode());
    }

    public function testCopyFileWithPrivilegeUsesSudoBash(): void
    {
        $ops = new class() extends UbuntuOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('sudo bash -lc', $command);
                $this->assertStringContainsString('cp -p ' . self::escapeShellArgument('/tmp/source.txt') . ' ' . self::escapeShellArgument('/tmp/dest.txt'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->copyFileWithPrivilege('/tmp/source.txt', '/tmp/dest.txt', 'secret');

        $this->assertSame(0, $result->getExitCode());
    }

    public function testGetFirewallStatusCommandIsReturned(): void
    {
        $ops = new class() extends UbuntuOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('if command -v ufw >/dev/null 2>&1; then', $command);
                $this->assertStringContainsString('elif command -v firewall-cmd >/dev/null 2>&1; then', $command);
                return new RemoteCommandOutput('status', '', 0);
            }
        };

        $result = $ops->getFirewallStatus();

        $this->assertSame('status', $result->getStdout());
    }
}
