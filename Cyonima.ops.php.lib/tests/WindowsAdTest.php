<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Windows\WindowsOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class WindowsAdTest extends TestCase
{
    public function testJoinDomainBuildsAddComputer(): void
    {
        $ops = new class() extends WindowsOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('Add-Computer -DomainName ' . self::escapePowerShellArgument('corp.example.local'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->joinDomain('corp.example.local', 'corp\\admin', 'Secret');
        $this->assertSame(0, $result->getExitCode());
    }

    public function testCreateAdUserBuildsNewAdUser(): void
    {
        $ops = new class() extends WindowsOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('New-ADUser -Name ' . self::escapePowerShellArgument('John Doe'), $command);
                $this->assertStringContainsString('-SamAccountName ' . self::escapePowerShellArgument('jdoe'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->createAdUser('jdoe', 'John Doe', 'P@ssw0rd', 'OU=Users,DC=corp,DC=example,DC=local');
        $this->assertSame(0, $result->getExitCode());
    }
}
