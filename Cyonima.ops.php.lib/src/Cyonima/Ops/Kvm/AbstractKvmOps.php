<?php

declare(strict_types=1);

namespace Cyonima\Ops\Kvm;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for KVM/libvirt operations via virsh over SSH
 *
 * Requires the `virsh` command-line tool (libvirt) to be installed and
 * available in the remote environment's PATH.
 */
abstract class AbstractKvmOps extends AbstractOps
{
    /**
     * Execute a virsh subcommand.
     *
     * @param string $subcommand virsh subcommand
     * @return RemoteCommandOutput
     */
    protected function virshCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('virsh ' . $subcommand);
    }

    /**
     * Get installed virsh/libvirt version.
     *
     * @return RemoteCommandOutput
     */
    public function getVirshVersion(): RemoteCommandOutput
    {
        return $this->virshCommand('version');
    }

    /**
     * List all virtual machines.
     *
     * @return RemoteCommandOutput
     */
    public function listVMs(): RemoteCommandOutput
    {
        return $this->virshCommand('list --all');
    }

    /**
     * Get the status of a virtual machine.
     *
     * @param string $vmName
     * @return RemoteCommandOutput
     */
    public function getVmStatus(string $vmName): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        return $this->virshCommand('domstate ' . $vmName);
    }

    /**
     * Get detailed information about a virtual machine.
     *
     * @param string $vmName
     * @return RemoteCommandOutput
     */
    public function getVmInfo(string $vmName): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        return $this->virshCommand('dominfo ' . $vmName);
    }

    /**
     * Start a virtual machine.
     *
     * @param string $vmName
     * @return RemoteCommandOutput
     */
    public function startVm(string $vmName): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        return $this->virshCommand('start ' . $vmName);
    }

    /**
     * Stop (gracefully shutdown) a virtual machine.
     *
     * @param string $vmName
     * @return RemoteCommandOutput
     */
    public function stopVm(string $vmName): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        return $this->virshCommand('shutdown ' . $vmName);
    }

    /**
     * Force stop (destroy) a virtual machine.
     *
     * @param string $vmName
     * @return RemoteCommandOutput
     */
    public function destroyVm(string $vmName): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        return $this->virshCommand('destroy ' . $vmName);
    }

    /**
     * Delete (undefine) a virtual machine.
     *
     * @param string $vmName
     * @return RemoteCommandOutput
     */
    public function deleteVm(string $vmName): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        return $this->virshCommand('undefine ' . $vmName);
    }

    /**
     * Create a virtual machine.
     *
     * @param string $vmName
     * @param string $vcpu Number of vCPUs
     * @param string $memory Memory in MB
     * @param string $imagePath Path to disk image
     * @return RemoteCommandOutput
     */
    public function createVm(string $vmName, string $vcpu, string $memory, string $imagePath): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $vcpu = self::escapeShellArgument($vcpu);
        $memory = self::escapeShellArgument($memory);
        $imagePath = self::escapeShellArgument($imagePath);

        return $this->virshCommand('define /dev/stdin <<< \'<domain type="kvm">' .
            '<name>' . $vmName . '</name>' .
            '<memory unit="MiB">' . $memory . '</memory>' .
            '<vcpu>' . $vcpu . '</vcpu>' .
            '<os><type arch="x86_64">hvm</type></os>' .
            '<devices>' .
            '<emulator>/usr/bin/qemu-system-x86_64</emulator>' .
            '<disk type="file" device="disk">' .
            '<driver name="qemu" type="qcow2"/>' .
            '<source file="' . $imagePath . '"/>' .
            '<target dev="vda" bus="virtio"/>' .
            '</disk>' .
            '<interface type="network">' .
            '<source network="default"/>' .
            '<model type="virtio"/>' .
            '</interface>' .
            '<console type="pty"><target type="virtio" port="0"/></console>' .
            '</devices>' .
            '</domain>\'');
    }

    /**
     * Set the number of vCPUs for a virtual machine.
     *
     * @param string $vmName
     * @param string $vcpu
     * @return RemoteCommandOutput
     */
    public function setVmCpu(string $vmName, string $vcpu): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $vcpu = self::escapeShellArgument($vcpu);
        return $this->virshCommand('setvcpus ' . $vmName . ' ' . $vcpu . ' --config');
    }

    /**
     * Set the memory (in MiB) for a virtual machine.
     *
     * @param string $vmName
     * @param string $memory Memory in MiB
     * @return RemoteCommandOutput
     */
    public function setVmMemory(string $vmName, string $memory): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $memory = self::escapeShellArgument($memory);
        return $this->virshCommand('setmem ' . $vmName . ' ' . $memory . ' --config');
    }

    /**
     * List all virtual networks.
     *
     * @return RemoteCommandOutput
     */
    public function listNetworks(): RemoteCommandOutput
    {
        return $this->virshCommand('net-list --all');
    }

    /**
     * List all storage pools.
     *
     * @return RemoteCommandOutput
     */
    public function listStoragePools(): RemoteCommandOutput
    {
        return $this->virshCommand('pool-list --all');
    }
}
