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
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('aws configure set aws_access_key_id ' . self::escapeShellArgument($accessKeyId), $command);
                $this->assertStringContainsString('aws configure set aws_secret_access_key ' . self::escapeShellArgument($secretAccessKey), $command);
                $this->assertStringContainsString('aws configure set region ' . self::escapeShellArgument('eu-west-1'), $command);
                $this->assertStringContainsString('aws configure set output ' . self::escapeShellArgument('json'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->configureCredentials($accessKeyId, $secretAccessKey, 'eu-west-1');

        $this->assertSame('', $result->getStdout());
        $this->assertSame(0, $result->getExitCode());
    }

    public function testCreateInstanceBuildsAwsRunInstancesCommand(): void
    {
        $ops = new class() extends AwsOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('aws ec2 run-instances --image-id ' . self::escapeShellArgument('ami-12345678'), $command);
                $this->assertStringContainsString('--instance-type ' . self::escapeShellArgument('t3.micro'), $command);
                $this->assertStringContainsString('--subnet-id ' . self::escapeShellArgument('subnet-01234567'), $command);
                $this->assertStringContainsString('--key-name ' . self::escapeShellArgument('my-key'), $command);
                $this->assertStringContainsString('--security-group-ids ' . self::escapeShellArgument('sg-01234567'), $command);
                return new RemoteCommandOutput('InstanceCreated', '', 0);
            }
        };

        $result = $ops->createInstance('my-instance', 'ami-12345678', 't3.micro', 'subnet-01234567', 'my-key', 'sg-01234567');

        $this->assertSame('InstanceCreated', $result->getStdout());
        $this->assertTrue($result->isSuccessful());
    }
}
