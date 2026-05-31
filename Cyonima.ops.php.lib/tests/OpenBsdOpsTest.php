<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Bsd\OpenBsdOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class OpenBsdOpsTest extends TestCase
{
    public function testInstallPackageUsesPkgAdd(): void
    {
        $ops = new class() extends OpenBsdOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertSame("pkg_add 'nano'", $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $ops->installPackage('nano');
    }

    public function testManageServiceStatusUsesServiceCommand(): void
    {
        $ops = new class() extends OpenBsdOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertSame("service 'httpd' status", $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $ops->getService('httpd');
    }

    public function testGetDistributionReturnsOpenBSD(): void
    {
        $ops = new class() extends OpenBsdOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $this->assertSame('OpenBSD', $ops->getDistribution());
    }
}
