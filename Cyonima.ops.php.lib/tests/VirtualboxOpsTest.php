<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Virtualbox\VirtualboxOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

class VirtualboxOpsTest extends TestCase
{
    public function testGetVirtualboxVersion(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('7.0.0', '', 0);
            }
        };

        $result = $ops->getVirtualboxVersion();

        $this->assertStringContainsString('VBoxManage --version', $ops->capturedCommand);
        $this->assertSame('7.0.0', $result->getStdout());
    }

    public function testListVMs(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('"vm1" {uuid1}', '', 0);
            }
        };

        $result = $ops->listVMs();

        $this->assertStringContainsString('VBoxManage list vms', $ops->capturedCommand);
        $this->assertStringContainsString('"vm1"', $result->getStdout());
    }

    public function testListRunningVMs(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('"vm1" {uuid1}', '', 0);
            }
        };

        $result = $ops->listRunningVMs();

        $this->assertStringContainsString('VBoxManage list runningvms', $ops->capturedCommand);
    }

    public function testGetVmStatus(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('running', '', 0);
            }
        };

        $result = $ops->getVmStatus('test-vm');

        $this->assertStringContainsString('showvminfo', $ops->capturedCommand);
        $this->assertStringContainsString('test-vm', $ops->capturedCommand);
    }

    public function testStartVm(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->startVm('test-vm', 'headless');

        $this->assertStringContainsString('startvm', $ops->capturedCommand);
        $this->assertStringContainsString('headless', $ops->capturedCommand);
    }

    public function testStopVm(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->stopVm('test-vm');

        $this->assertStringContainsString('controlvm', $ops->capturedCommand);
        $this->assertStringContainsString('acpipowerbutton', $ops->capturedCommand);
    }

    public function testPowerOffVm(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->powerOffVm('test-vm');

        $this->assertStringContainsString('controlvm', $ops->capturedCommand);
        $this->assertStringContainsString('poweroff', $ops->capturedCommand);
    }

    public function testDeleteVm(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->deleteVm('test-vm', false);

        $this->assertStringContainsString('unregistervm', $ops->capturedCommand);
    }

    public function testSetVmCpu(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->setVmCpu('test-vm', '4');

        $this->assertStringContainsString('modifyvm', $ops->capturedCommand);
        $this->assertStringContainsString('--cpus', $ops->capturedCommand);
    }

    public function testSetVmMemory(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->setVmMemory('test-vm', '4096');

        $this->assertStringContainsString('modifyvm', $ops->capturedCommand);
        $this->assertStringContainsString('--memory', $ops->capturedCommand);
    }

    public function testListNetworks(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->listNetworks();

        $this->assertStringContainsString('list hostonlyifs', $ops->capturedCommand);
    }

    public function testCloneVm(): void
    {
        $ops = new class() extends VirtualboxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->cloneVm('source-vm', 'cloned-vm', 'machine');

        $this->assertStringContainsString('clonevm', $ops->capturedCommand);
        $this->assertStringContainsString('source-vm', $ops->capturedCommand);
        $this->assertStringContainsString('cloned-vm', $ops->capturedCommand);
    }
}
