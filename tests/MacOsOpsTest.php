<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\MacOS\MacOsOps;
use Cyonima\Ops\RemoteCommandOutput;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MacOsOpsTest extends TestCase
{
    public function testGetMacOsVersionExecutesSwVers(): void
    {
        $expectedOutput = new RemoteCommandOutput("13.6.1\n", '', 0);

        $ops = new class($expectedOutput) extends MacOsOps {
            private RemoteCommandOutput $expectedOutput;
            public string $capturedCommand = '';

            public function __construct(RemoteCommandOutput $expectedOutput)
            {
                parent::__construct();
                $this->expectedOutput = $expectedOutput;
            }

            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return $this->expectedOutput;
            }
        };

        $result = $ops->getMacOsVersion();

        $this->assertSame('sw_vers -productVersion', $ops->capturedCommand);
        $this->assertSame($expectedOutput, $result);
    }

    public function testManageServiceRejectsUnsupportedAction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $ops = new class() extends MacOsOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                throw new \LogicException('remoteExec should not be called for unsupported action');
            }
        };

        $ops->manageService('nginx', 'invalid-action');
    }
}
