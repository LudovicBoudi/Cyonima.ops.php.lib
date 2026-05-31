<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Gcp\GcpOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class GcpOpsTest extends TestCase
{
    public function testAuthenticateWithServiceAccountKeyBuildsGcloudAuthCommand(): void
    {
        $path = '/tmp/service-account.json';

        $ops = new class() extends GcpOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('gcloud auth activate-service-account', $command);
                $this->assertStringContainsString('--key-file ' . self::escapeShellArgument($path), $command);
                return new RemoteCommandOutput('Activated', '', 0);
            }
        };

        $result = $ops->authenticateWithServiceAccountKey($path);

        $this->assertSame('Activated', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testCreateInstanceBuildsGcloudComputeCreateCommand(): void
    {
        $ops = new class() extends GcpOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('gcloud compute instances create ' . self::escapeShellArgument('my-instance'), $command);
                $this->assertStringContainsString('--zone ' . self::escapeShellArgument('europe-west1-b'), $command);
                $this->assertStringContainsString('--machine-type ' . self::escapeShellArgument('e2-medium'), $command);
                $this->assertStringContainsString('--image-family ' . self::escapeShellArgument('ubuntu-2004-lts'), $command);
                $this->assertStringContainsString('--image-project ' . self::escapeShellArgument('ubuntu-os-cloud'), $command);
                $this->assertStringContainsString('--network ' . self::escapeShellArgument('default'), $command);
                return new RemoteCommandOutput('name: my-instance', '', 0);
            }
        };

        $result = $ops->createInstance('my-instance', 'europe-west1-b', 'e2-medium', 'ubuntu-2004-lts', 'ubuntu-os-cloud', 'default');

        $this->assertSame('name: my-instance', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
