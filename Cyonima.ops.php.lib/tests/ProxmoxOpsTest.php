<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Proxmox\ProxmoxOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

class ProxmoxOpsTest extends TestCase
{
    public function testGetProxmoxVersion(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('8.0', '', 0);
            }
        };

        $result = $ops->getProxmoxVersion();

        $this->assertSame("pvesh get /version", $ops->capturedCommand);
        $this->assertSame('8.0', $result->getStdout());
    }

    public function testListNodes(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('pve1', '', 0);
            }
        };

        $result = $ops->listNodes();

        $this->assertSame("pvesh get /nodes", $ops->capturedCommand);
    }

    public function testListVMs(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('VM list', '', 0);
            }
        };

        $result = $ops->listVMs();

        $this->assertSame("qm list", $ops->capturedCommand);
    }

    public function testGetVmStatus(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('running', '', 0);
            }
        };

        $result = $ops->getVmStatus('100');

        $this->assertSame("qm status '100'", $ops->capturedCommand);
    }

    public function testGetVmConfig(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('config...', '', 0);
            }
        };

        $result = $ops->getVmConfig('100');

        $this->assertSame("qm config '100'", $ops->capturedCommand);
    }

    public function testStartVm(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->startVm('100');

        $this->assertSame("qm start '100'", $ops->capturedCommand);
    }

    public function testStopVm(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->stopVm('100');

        $this->assertSame("qm shutdown '100'", $ops->capturedCommand);
    }

    public function testKillVm(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->killVm('100');

        $this->assertSame("qm stop '100'", $ops->capturedCommand);
    }

    public function testDeleteVm(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->deleteVm('100');

        $this->assertSame("qm destroy '100'", $ops->capturedCommand);
    }

    public function testSetVmCpu(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->setVmCpu('100', '4');

        $this->assertSame("qm set '100' --cores '4'", $ops->capturedCommand);
    }

    public function testSetVmMemory(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->setVmMemory('100', '4096');

        $this->assertSame("qm set '100' --memory '4096'", $ops->capturedCommand);
    }

    public function testListStorages(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('storage...', '', 0);
            }
        };

        $result = $ops->listStorages();

        $this->assertSame("pvesh get /storage", $ops->capturedCommand);
    }

    public function testGetClusterStatus(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('quorate', '', 0);
            }
        };

        $result = $ops->getClusterStatus();

        $this->assertSame("pvesh get /cluster/status", $ops->capturedCommand);
    }

    public function testCreateSnapshot(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->createSnapshot('100', 'backup-2026-06-01');

        $this->assertSame("qm snapshot '100' --snapname 'backup-2026-06-01'", $ops->capturedCommand);
    }

    public function testListSnapshots(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('snapshots...', '', 0);
            }
        };

        $result = $ops->listSnapshots('100');

        $this->assertSame("qm listsnapshot '100'", $ops->capturedCommand);
    }

    public function testCloneVm(): void
    {
        $ops = new class() extends ProxmoxOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->cloneVm('100', '101', 'cloned-vm');

        $this->assertSame("qm clone '100' '101' --name 'cloned-vm'", $ops->capturedCommand);
    }
}
