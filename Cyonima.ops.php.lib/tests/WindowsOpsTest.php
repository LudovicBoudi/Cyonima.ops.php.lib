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
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('Get-Content -Path ' . self::escapePowerShellArgument($path), $command);
                return new RemoteCommandOutput('log line', '', 0);
            }
        };

        $result = $ops->readFile($path);

        $this->assertSame('log line', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testWriteFileEncodesContentAndUsesSetContent(): void
    {
        $path = 'C:\\temp\\out.txt';
        $content = "hello windows\n";
        $encoded = base64_encode($content);

        $ops = new class() extends WindowsOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('Set-Content -Path ' . self::escapePowerShellArgument('C:\\temp\\out.txt'), $command);
                $this->assertStringContainsString(self::escapePowerShellArgument($encoded), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->writeFile($path, $content);

        $this->assertSame(0, $result->getExitCode());
    }

    public function testGetProcessesBuildsPowerShellJsonCommand(): void
    {
        $ops = new class() extends WindowsOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('Get-Process', $command);
                $this->assertStringContainsString('| ConvertTo-Json -Compress', $command);
                return new RemoteCommandOutput('[{"Id":123,"ProcessName":"php"}]', '', 0);
            }
        };

        $result = $ops->getProcesses('php');

        $this->assertSame('[{"Id":123,"ProcessName":"php"}]', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testAddFirewallRuleBuildsNewNetFirewallRule(): void
    {
        $ops = new class() extends WindowsOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('New-NetFirewallRule -Name ' . self::escapePowerShellArgument('AllowSSH'), $command);
                $this->assertStringContainsString('-Protocol ' . self::escapePowerShellArgument('TCP'), $command);
                $this->assertStringContainsString('-LocalPort ' . self::escapePowerShellArgument('22'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->addFirewallRule('AllowSSH', 'Allow SSH Access', 'Inbound', 'TCP', '22');

        $this->assertSame(0, $result->getExitCode());
    }
}
