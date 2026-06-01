<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Azure\AzureOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class AzureOpsTest extends TestCase
{
    public function testLoginWithServicePrincipalBuildsAzLoginCommand(): void
    {
        $tenantId = '00000000-0000-0000-0000-000000000000';
        $clientId = '00000000-0000-0000-0000-000000000000';
        $clientSecret = 'secret-password';

        $ops = new class() extends AzureOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('[]', '', 0);
            }
        };

        $result = $ops->loginWithServicePrincipal($tenantId, $clientId, $clientSecret);

        $this->assertStringContainsString('az login --service-principal', $ops->capturedCommand);
        $this->assertStringContainsString('--tenant', $ops->capturedCommand);
        $this->assertStringContainsString('--username', $ops->capturedCommand);
        $this->assertStringContainsString('--password', $ops->capturedCommand);
        $this->assertSame('[]', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testCreateResourceGroupBuildsGroupCreateCommand(): void
    {
        $ops = new class() extends AzureOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('{"name":"my-rg"}', '', 0);
            }
        };

        $result = $ops->createResourceGroup('my-rg', 'westeurope');

        $this->assertStringContainsString('az group create --name', $ops->capturedCommand);
        $this->assertStringContainsString('--location', $ops->capturedCommand);
        $this->assertSame('{"name":"my-rg"}', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testListVirtualMachinesBuildsVmListCommand(): void
    {
        $ops = new class() extends AzureOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('[]', '', 0);
            }
        };

        $result = $ops->listVirtualMachines('my-rg');

        $this->assertStringContainsString('az vm list --show-details --output json', $ops->capturedCommand);
        $this->assertStringContainsString('--resource-group', $ops->capturedCommand);
        $this->assertSame('[]', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
