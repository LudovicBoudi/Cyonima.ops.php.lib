<?php

declare(strict_types=1);

namespace Cyonima\Tests;

use Cyonima\Ops\Kvm\KvmOps;
use PHPUnit\Framework\TestCase;

class KvmOpsTest extends TestCase
{
    private KvmOps $kvm;

    protected function setUp(): void
    {
        $this->kvm = new KvmOps();
        $this->kvm->setHost('127.0.0.1')
            ->setCredentials('testuser', 'testpass')
            ->setSshPort(22);
    }

    /**
     * Test getVirshVersion() command generation.
     */
    public function testGetVirshVersion(): void
    {
        $output = $this->kvm->getVirshVersion();
        $this->assertIsObject($output);
    }

    /**
     * Test listVMs() command generation.
     */
    public function testListVMs(): void
    {
        $output = $this->kvm->listVMs();
        $this->assertIsObject($output);
    }

    /**
     * Test getVmStatus() with VM name.
     */
    public function testGetVmStatus(): void
    {
        $output = $this->kvm->getVmStatus('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test getVmInfo() with VM name.
     */
    public function testGetVmInfo(): void
    {
        $output = $this->kvm->getVmInfo('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test startVm() with VM name.
     */
    public function testStartVm(): void
    {
        $output = $this->kvm->startVm('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test stopVm() with VM name.
     */
    public function testStopVm(): void
    {
        $output = $this->kvm->stopVm('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test destroyVm() with VM name.
     */
    public function testDestroyVm(): void
    {
        $output = $this->kvm->destroyVm('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test deleteVm() with VM name.
     */
    public function testDeleteVm(): void
    {
        $output = $this->kvm->deleteVm('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test setVmCpu() with VM name and CPU count.
     */
    public function testSetVmCpu(): void
    {
        $output = $this->kvm->setVmCpu('test-vm', '4');
        $this->assertIsObject($output);
    }

    /**
     * Test setVmMemory() with VM name and memory value.
     */
    public function testSetVmMemory(): void
    {
        $output = $this->kvm->setVmMemory('test-vm', '4096');
        $this->assertIsObject($output);
    }

    /**
     * Test listNetworks() command generation.
     */
    public function testListNetworks(): void
    {
        $output = $this->kvm->listNetworks();
        $this->assertIsObject($output);
    }

    /**
     * Test listStoragePools() command generation.
     */
    public function testListStoragePools(): void
    {
        $output = $this->kvm->listStoragePools();
        $this->assertIsObject($output);
    }
}
