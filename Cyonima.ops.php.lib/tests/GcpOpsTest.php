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
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('Activated', '', 0);
            }
        };

        $result = $ops->authenticateWithServiceAccountKey($path);

        $this->assertStringContainsString('gcloud auth activate-service-account', $ops->capturedCommand);
        $this->assertStringContainsString('--key-file', $ops->capturedCommand);
        $this->assertSame('Activated', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }

    public function testCreateInstanceBuildsGcloudComputeCreateCommand(): void
    {
        $ops = new class() extends GcpOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('name: my-instance', '', 0);
            }
        };

        $result = $ops->createInstance('my-instance', 'europe-west1-b', 'e2-medium', 'ubuntu-2004-lts', 'ubuntu-os-cloud', 'default');

        $this->assertStringContainsString('gcloud compute instances create', $ops->capturedCommand);
        $this->assertStringContainsString('--zone', $ops->capturedCommand);
        $this->assertStringContainsString('--machine-type', $ops->capturedCommand);
        $this->assertStringContainsString('--image-family', $ops->capturedCommand);
        $this->assertStringContainsString('--image-project', $ops->capturedCommand);
        $this->assertStringContainsString('--network', $ops->capturedCommand);
        $this->assertSame('name: my-instance', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
