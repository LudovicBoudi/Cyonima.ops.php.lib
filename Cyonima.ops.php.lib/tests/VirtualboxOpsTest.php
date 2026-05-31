<?php

declare(strict_types=1);

namespace Cyonima\Tests;

use Cyonima\Ops\Virtualbox\VirtualboxOps;
use PHPUnit\Framework\TestCase;

class VirtualboxOpsTest extends TestCase
{
    private VirtualboxOps $vbox;

    protected function setUp(): void
    {
        $this->vbox = new VirtualboxOps();
        $this->vbox->setHost('127.0.0.1')
            ->setCredentials('testuser', 'testpass')
            ->setSshPort(22);
    }

    /**
     * Test getVirtualboxVersion() command generation.
     */
    public function testGetVirtualboxVersion(): void
    {
        $output = $this->vbox->getVirtualboxVersion();
        $this->assertIsObject($output);
    }

    /**
     * Test listVMs() command generation.
     */
    public function testListVMs(): void
    {
        $output = $this->vbox->listVMs();
        $this->assertIsObject($output);
    }

    /**
     * Test listRunningVMs() command generation.
     */
    public function testListRunningVMs(): void
    {
        $output = $this->vbox->listRunningVMs();
        $this->assertIsObject($output);
    }

    /**
     * Test getVmStatus() with VM name.
     */
    public function testGetVmStatus(): void
    {
        $output = $this->vbox->getVmStatus('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test getVmInfo() with VM name.
     */
    public function testGetVmInfo(): void
    {
        $output = $this->vbox->getVmInfo('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test startVm() with VM name and type.
     */
    public function testStartVm(): void
    {
        $output = $this->vbox->startVm('test-vm', 'headless');
        $this->assertIsObject($output);
    }

    /**
     * Test stopVm() with VM name.
     */
    public function testStopVm(): void
    {
        $output = $this->vbox->stopVm('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test powerOffVm() with VM name.
     */
    public function testPowerOffVm(): void
    {
        $output = $this->vbox->powerOffVm('test-vm');
        $this->assertIsObject($output);
    }

    /**
     * Test deleteVm() with VM name.
     */
    public function testDeleteVm(): void
    {
        $output = $this->vbox->deleteVm('test-vm', false);
        $this->assertIsObject($output);
    }

    /**
     * Test setVmCpu() with VM name and CPU count.
     */
    public function testSetVmCpu(): void
    {
        $output = $this->vbox->setVmCpu('test-vm', '4');
        $this->assertIsObject($output);
    }

    /**
     * Test setVmMemory() with VM name and memory value.
     */
    public function testSetVmMemory(): void
    {
        $output = $this->vbox->setVmMemory('test-vm', '4096');
        $this->assertIsObject($output);
    }

    /**
     * Test listNetworks() command generation.
     */
    public function testListNetworks(): void
    {
        $output = $this->vbox->listNetworks();
        $this->assertIsObject($output);
    }

    /**
     * Test cloneVm() with source and target VM names.
     */
    public function testCloneVm(): void
    {
        $output = $this->vbox->cloneVm('source-vm', 'cloned-vm', 'machine');
        $this->assertIsObject($output);
    }
}
