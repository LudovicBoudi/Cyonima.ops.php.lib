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
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('line1', '', 0);
            }
        };

        $result = $ops->readFile($path);

        $this->assertSame("cat '/tmp/example.log'", $ops->capturedCommand);
        $this->assertSame('line1', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testWriteFileEncodesContentAndWritesToDestination(): void
    {
        $path = '/tmp/out.txt';
        $content = "hello world\n";

        $ops = new class() extends UbuntuOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->writeFile($path, $content);

        $this->assertStringContainsString('base64 --decode > ', $ops->capturedCommand);
        $this->assertStringContainsString("'/tmp/out.txt'", $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }

    public function testCopyFileWithPrivilegeUsesSudoBash(): void
    {
        $ops = new class() extends UbuntuOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->copyFileWithPrivilege('/tmp/source.txt', '/tmp/dest.txt', 'secret');

        $this->assertStringContainsString('sudo -S bash -lc', $ops->capturedCommand);
        $this->assertStringContainsString('cp -p ', $ops->capturedCommand);
        $this->assertStringContainsString('rm -f', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }

    public function testGetFirewallStatusCommandIsReturned(): void
    {
        $ops = new class() extends UbuntuOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('status', '', 0);
            }
        };

        $result = $ops->getFirewallStatus();

        $this->assertStringContainsString('if command -v ufw >/dev/null 2>&1; then', $ops->capturedCommand);
        $this->assertStringContainsString('elif command -v firewall-cmd >/dev/null 2>&1; then', $ops->capturedCommand);
        $this->assertSame('status', $result->getStdout());
    }
}
