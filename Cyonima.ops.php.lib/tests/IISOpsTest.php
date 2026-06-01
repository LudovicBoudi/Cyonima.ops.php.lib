<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Windows\IISOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class IISOpsTest extends TestCase
{
    public function testInstallIISBuildsInstallWindowsFeature(): void
    {
        $ops = new class() extends IISOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->installIIS();

        $this->assertStringContainsString('Install-WindowsFeature -Name Web-Server', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }

    public function testCreateWebsiteBuildsNewWebsite(): void
    {
        $ops = new class() extends IISOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->createWebsite('MySite', 'C:\\inetpub\\wwwroot\\mysite', 8080);

        $this->assertStringContainsString('New-Website -Name', $ops->capturedCommand);
        $this->assertStringContainsString('MySite', $ops->capturedCommand);
        $this->assertStringContainsString('-PhysicalPath', $ops->capturedCommand);
        $this->assertStringContainsString('-Port', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
