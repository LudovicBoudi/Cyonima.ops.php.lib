<?php

declare(strict_types=1);

namespace Cyonima\Tests;

use Cyonima\Ops\Proxmox\ProxmoxOps;
use PHPUnit\Framework\TestCase;

class ProxmoxOpsTest extends TestCase
{
    private ProxmoxOps $proxmox;

    protected function setUp(): void
    {
        $this->proxmox = new ProxmoxOps();
        $this->proxmox->setHost('127.0.0.1')
            ->setCredentials('root', 'testpass')
            ->setSshPort(22);
    }

    /**
     * Test getProxmoxVersion() command generation.
     */
    public function testGetProxmoxVersion(): void
    {
        $output = $this->proxmox->getProxmoxVersion();
        $this->assertIsObject($output);
    }

    /**
     * Test listNodes() command generation.
     */
    public function testListNodes(): void
    {
        $output = $this->proxmox->listNodes();
        $this->assertIsObject($output);
    }

    /**
     * Test listVMs() command generation.
     */
    public function testListVMs(): void
    {
        $output = $this->proxmox->listVMs();
        $this->assertIsObject($output);
    }

    /**
     * Test getVmStatus() with VM ID.
     */
    public function testGetVmStatus(): void
    {
        $output = $this->proxmox->getVmStatus('100');
        $this->assertIsObject($output);
    }

    /**
     * Test getVmConfig() with VM ID.
     */
    public function testGetVmConfig(): void
    {
        $output = $this->proxmox->getVmConfig('100');
        $this->assertIsObject($output);
    }

    /**
     * Test startVm() with VM ID.
     */
    public function testStartVm(): void
    {
        $output = $this->proxmox->startVm('100');
        $this->assertIsObject($output);
    }

    /**
     * Test stopVm() with VM ID.
     */
    public function testStopVm(): void
    {
        $output = $this->proxmox->stopVm('100');
        $this->assertIsObject($output);
    }

    /**
     * Test killVm() with VM ID.
     */
    public function testKillVm(): void
    {
        $output = $this->proxmox->killVm('100');
        $this->assertIsObject($output);
    }

    /**
     * Test deleteVm() with VM ID.
     */
    public function testDeleteVm(): void
    {
        $output = $this->proxmox->deleteVm('100');
        $this->assertIsObject($output);
    }

    /**
     * Test setVmCpu() with VM ID and cores.
     */
    public function testSetVmCpu(): void
    {
        $output = $this->proxmox->setVmCpu('100', '4');
        $this->assertIsObject($output);
    }

    /**
     * Test setVmMemory() with VM ID and memory value.
     */
    public function testSetVmMemory(): void
    {
        $output = $this->proxmox->setVmMemory('100', '4096');
        $this->assertIsObject($output);
    }

    /**
     * Test listStorages() command generation.
     */
    public function testListStorages(): void
    {
        $output = $this->proxmox->listStorages();
        $this->assertIsObject($output);
    }

    /**
     * Test getClusterStatus() command generation.
     */
    public function testGetClusterStatus(): void
    {
        $output = $this->proxmox->getClusterStatus();
        $this->assertIsObject($output);
    }

    /**
     * Test createSnapshot() with VM ID and snapshot name.
     */
    public function testCreateSnapshot(): void
    {
        $output = $this->proxmox->createSnapshot('100', 'backup-2026-06-01');
        $this->assertIsObject($output);
    }

    /**
     * Test listSnapshots() with VM ID.
     */
    public function testListSnapshots(): void
    {
        $output = $this->proxmox->listSnapshots('100');
        $this->assertIsObject($output);
    }

    /**
     * Test cloneVm() with source and target VM IDs.
     */
    public function testCloneVm(): void
    {
        $output = $this->proxmox->cloneVm('100', '101', 'cloned-vm');
        $this->assertIsObject($output);
    }
}
