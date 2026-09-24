<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Windows\WindowsOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class WindowsOpsTest extends TestCase
{
    public function testReadFileUsesGetContentWithPath(): void
    {
        $path = 'C:\\temp\\example.log';
        $ops = new class() extends WindowsOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('log line', '', 0);
            }
        };

        $result = $ops->readFile($path);

        $this->assertStringContainsString('Get-Content -Path', $ops->capturedCommand);
        $this->assertStringContainsString('C:\temp\example.log', $ops->capturedCommand);
        $this->assertSame('log line', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testWriteFileEncodesContentAndUsesSetContent(): void
    {
        $path = 'C:\\temp\\out.txt';
        $content = "hello windows\n";

        $ops = new class() extends WindowsOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->writeFile($path, $content);

        $this->assertStringContainsString('Set-Content -Path', $ops->capturedCommand);
        $this->assertStringContainsString('C:\temp\out.txt', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }

    public function testGetProcessesBuildsPowerShellJsonCommand(): void
    {
        $ops = new class() extends WindowsOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('[{"Id":123,"ProcessName":"php"}]', '', 0);
            }
        };

        $result = $ops->getProcesses('php');

        $this->assertStringContainsString('Get-Process', $ops->capturedCommand);
        $this->assertStringContainsString('ConvertTo-Json -Compress', $ops->capturedCommand);
        $this->assertSame('[{"Id":123,"ProcessName":"php"}]', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testAddFirewallRuleBuildsNewNetFirewallRule(): void
    {
        $ops = new class() extends WindowsOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->addFirewallRule('AllowSSH', 'Allow SSH Access', 'Inbound', 'TCP', '22');

        $this->assertStringContainsString('New-NetFirewallRule -Name', $ops->capturedCommand);
        $this->assertStringContainsString('-Protocol', $ops->capturedCommand);
        $this->assertStringContainsString('-LocalPort', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
