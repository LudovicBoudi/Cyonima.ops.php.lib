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
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('Install-WindowsFeature -Name Web-Server', $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->installIIS();
        $this->assertSame(0, $result->getExitCode());
    }

    public function testCreateWebsiteBuildsNewWebsite(): void
    {
        $ops = new class() extends IISOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('New-Website -Name ' . self::escapePowerShellArgument('MySite'), $command);
                $this->assertStringContainsString(' -PhysicalPath ' . self::escapePowerShellArgument('C:\\inetpub\\wwwroot\\mysite'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->createWebsite('MySite', 'C:\\inetpub\\wwwroot\\mysite', 8080);
        $this->assertSame(0, $result->getExitCode());
    }
}
