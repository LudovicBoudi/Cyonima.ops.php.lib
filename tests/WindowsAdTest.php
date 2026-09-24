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
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->joinDomain('corp.example.local', 'corp\\admin', 'Secret');

        $this->assertStringContainsString('Add-Computer -DomainName', $ops->capturedCommand);
        $this->assertStringContainsString('corp.example.local', $ops->capturedCommand);
        $this->assertStringContainsString('-Credential', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }

    public function testCreateAdUserBuildsNewAdUser(): void
    {
        $ops = new class() extends WindowsOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->createAdUser('jdoe', 'John Doe', 'P@ssw0rd', 'OU=Users,DC=corp,DC=example,DC=local');

        $this->assertStringContainsString('New-ADUser -Name', $ops->capturedCommand);
        $this->assertStringContainsString('John Doe', $ops->capturedCommand);
        $this->assertStringContainsString('-SamAccountName', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
