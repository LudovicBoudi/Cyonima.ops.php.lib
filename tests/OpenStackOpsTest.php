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
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('id: 1234', '', 0);
            }
        };

        $ops->setOpenStackAuthentication('https://openstack.example.local:5000/v3', 'demo', 'admin', 'secret');
        $result = $ops->authenticate();

        $this->assertStringContainsString('openstack --os-auth-url', $ops->capturedCommand);
        $this->assertStringContainsString('--os-project-name', $ops->capturedCommand);
        $this->assertStringContainsString('--os-username', $ops->capturedCommand);
        $this->assertStringContainsString('--os-password', $ops->capturedCommand);
        $this->assertStringContainsString('token issue', $ops->capturedCommand);
        $this->assertSame('id: 1234', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testCreateServerBuildsServerCreateCommand(): void
    {
        $ops = new class() extends OpenStackOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('{
  "id": "1234"
}', '', 0);
            }
        };

        $ops->setOpenStackAuthentication('https://openstack.example.local:5000/v3', 'demo', 'admin', 'secret');
        $result = $ops->createServer('my-vm', 'Ubuntu20.04', 'm1.small', 'private-net', 'my-key');

        $this->assertStringContainsString('server create --wait', $ops->capturedCommand);
        $this->assertStringContainsString('--image', $ops->capturedCommand);
        $this->assertStringContainsString('--flavor', $ops->capturedCommand);
        $this->assertStringContainsString('--network', $ops->capturedCommand);
        $this->assertStringContainsString('--key-name', $ops->capturedCommand);
        $this->assertStringContainsString('--format json', $ops->capturedCommand);
        $this->assertSame('{
  "id": "1234"
}', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
