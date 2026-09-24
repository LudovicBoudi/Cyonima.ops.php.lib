<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Kvm\KvmOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

class KvmOpsTest extends TestCase
{
    public function testGetVirshVersion(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('8.0.0', '', 0);
            }
        };

        $result = $ops->getVirshVersion();

        $this->assertStringContainsString('virsh version', $ops->capturedCommand);
    }

    public function testListVMs(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('vm1', '', 0);
            }
        };

        $result = $ops->listVMs();

        $this->assertStringContainsString('virsh list --all', $ops->capturedCommand);
    }

    public function testGetVmStatus(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('running', '', 0);
            }
        };

        $result = $ops->getVmStatus('test-vm');

        $this->assertStringContainsString('virsh domstate', $ops->capturedCommand);
    }

    public function testGetVmInfo(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('info...', '', 0);
            }
        };

        $result = $ops->getVmInfo('test-vm');

        $this->assertStringContainsString('virsh dominfo', $ops->capturedCommand);
    }

    public function testStartVm(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->startVm('test-vm');

        $this->assertStringContainsString('virsh start', $ops->capturedCommand);
    }

    public function testStopVm(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->stopVm('test-vm');

        $this->assertStringContainsString('virsh shutdown', $ops->capturedCommand);
    }

    public function testDestroyVm(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->destroyVm('test-vm');

        $this->assertStringContainsString('virsh destroy', $ops->capturedCommand);
    }

    public function testDeleteVm(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->deleteVm('test-vm');

        $this->assertStringContainsString('virsh undefine', $ops->capturedCommand);
    }

    public function testSetVmCpu(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->setVmCpu('test-vm', '4');

        $this->assertStringContainsString('virsh setvcpus', $ops->capturedCommand);
    }

    public function testSetVmMemory(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->setVmMemory('test-vm', '4096');

        $this->assertStringContainsString('virsh setmem', $ops->capturedCommand);
    }

    public function testListNetworks(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('default', '', 0);
            }
        };

        $result = $ops->listNetworks();

        $this->assertStringContainsString('virsh net-list --all', $ops->capturedCommand);
    }

    public function testListStoragePools(): void
    {
        $ops = new class() extends KvmOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('default', '', 0);
            }
        };

        $result = $ops->listStoragePools();

        $this->assertStringContainsString('virsh pool-list --all', $ops->capturedCommand);
    }
}
