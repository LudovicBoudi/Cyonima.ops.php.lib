<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\OpenStack\OpenStackOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class OpenStackOpsTest extends TestCase
{
    public function testAuthenticateBuildsOpenStackTokenIssueCommand(): void
    {
        $ops = new class() extends OpenStackOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('openstack --os-auth-url ' . self::escapeShellArgument('https://openstack.example.local:5000/v3'), $command);
                $this->assertStringContainsString('--os-project-name ' . self::escapeShellArgument('demo'), $command);
                $this->assertStringContainsString('--os-username ' . self::escapeShellArgument('admin'), $command);
                $this->assertStringContainsString('--os-password ' . self::escapeShellArgument('secret'), $command);
                $this->assertStringContainsString('token issue', $command);
                return new RemoteCommandOutput('id: 1234', '', 0);
            }
        };

        $ops->setOpenStackAuthentication('https://openstack.example.local:5000/v3', 'demo', 'admin', 'secret');
        $result = $ops->authenticate();

        $this->assertSame('id: 1234', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testCreateServerBuildsServerCreateCommand(): void
    {
        $ops = new class() extends OpenStackOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('server create --wait ' . self::escapeShellArgument('my-vm'), $command);
                $this->assertStringContainsString('--image ' . self::escapeShellArgument('Ubuntu20.04'), $command);
                $this->assertStringContainsString('--flavor ' . self::escapeShellArgument('m1.small'), $command);
                $this->assertStringContainsString('--network ' . self::escapeShellArgument('private-net'), $command);
                $this->assertStringContainsString('--key-name ' . self::escapeShellArgument('my-key'), $command);
                $this->assertStringContainsString('--format json', $command);
                return new RemoteCommandOutput('{
  "id": "1234"
}', '', 0);
            }
        };

        $ops->setOpenStackAuthentication('https://openstack.example.local:5000/v3', 'demo', 'admin', 'secret');
        $result = $ops->createServer('my-vm', 'Ubuntu20.04', 'm1.small', 'private-net', 'my-key');

        $this->assertSame('{
  "id": "1234"
}', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
