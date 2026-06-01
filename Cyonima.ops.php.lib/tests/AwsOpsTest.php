<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Aws\AwsOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class AwsOpsTest extends TestCase
{
    public function testConfigureCredentialsBuildsAwsConfigureCommands(): void
    {
        $accessKeyId = 'AKIAEXAMPLE';
        $secretAccessKey = 'secret123';

        $ops = new class() extends AwsOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->configureCredentials($accessKeyId, $secretAccessKey, 'eu-west-1');

        $this->assertStringContainsString('aws configure set aws_access_key_id', $ops->capturedCommand);
        $this->assertStringContainsString('aws configure set aws_secret_access_key', $ops->capturedCommand);
        $this->assertStringContainsString('aws configure set region', $ops->capturedCommand);
        $this->assertStringContainsString('aws configure set output', $ops->capturedCommand);
        $this->assertSame('', $result->getStdout());
        $this->assertSame(0, $result->getExitCode());
    }

    public function testCreateInstanceBuildsAwsRunInstancesCommand(): void
    {
        $ops = new class() extends AwsOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('InstanceCreated', '', 0);
            }
        };

        $result = $ops->createInstance('my-instance', 'ami-12345678', 't3.micro', 'subnet-01234567', 'my-key', 'sg-01234567');

        $this->assertStringContainsString('ec2 run-instances --image-id', $ops->capturedCommand);
        $this->assertStringContainsString('--instance-type', $ops->capturedCommand);
        $this->assertStringContainsString('--subnet-id', $ops->capturedCommand);
        $this->assertStringContainsString('--key-name', $ops->capturedCommand);
        $this->assertStringContainsString('--security-group-ids', $ops->capturedCommand);
        $this->assertSame('InstanceCreated', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
