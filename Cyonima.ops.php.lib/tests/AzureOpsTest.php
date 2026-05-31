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
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('az login --service-principal', $command);
                $this->assertStringContainsString('--tenant ' . self::escapeShellArgument('00000000-0000-0000-0000-000000000000'), $command);
                $this->assertStringContainsString('--username ' . self::escapeShellArgument('00000000-0000-0000-0000-000000000000'), $command);
                $this->assertStringContainsString('--password ' . self::escapeShellArgument('secret-password'), $command);
                return new RemoteCommandOutput('[]', '', 0);
            }
        };

        $result = $ops->loginWithServicePrincipal($tenantId, $clientId, $clientSecret);

        $this->assertSame('[]', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testCreateResourceGroupBuildsGroupCreateCommand(): void
    {
        $ops = new class() extends AzureOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('az group create --name ' . self::escapeShellArgument('my-rg'), $command);
                $this->assertStringContainsString('--location ' . self::escapeShellArgument('westeurope'), $command);
                return new RemoteCommandOutput('{"name":"my-rg"}', '', 0);
            }
        };

        $result = $ops->createResourceGroup('my-rg', 'westeurope');

        $this->assertSame('{"name":"my-rg"}', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testListVirtualMachinesBuildsVmListCommand(): void
    {
        $ops = new class() extends AzureOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('az vm list --show-details --output json', $command);
                $this->assertStringContainsString('--resource-group ' . self::escapeShellArgument('my-rg'), $command);
                return new RemoteCommandOutput('[]', '', 0);
            }
        };

        $result = $ops->listVirtualMachines('my-rg');

        $this->assertSame('[]', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
