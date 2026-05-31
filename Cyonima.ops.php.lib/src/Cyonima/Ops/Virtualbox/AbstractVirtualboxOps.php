<?php

declare(strict_types=1);

namespace Cyonima\Ops\Virtualbox;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for VirtualBox operations via VBoxManage over SSH
 *
 * Requires the `VBoxManage` command-line tool (VirtualBox) to be installed and
 * available in the remote environment's PATH.
 */
abstract class AbstractVirtualboxOps extends AbstractOps
{
    /**
     * Execute a VBoxManage subcommand.
     *
     * @param string $subcommand VBoxManage subcommand
     * @return RemoteCommandOutput
     */
    protected function vboxCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('VBoxManage ' . $subcommand);
    }

    /**
     * Get installed VirtualBox version.
     *
     * @return RemoteCommandOutput
     */
    public function getVirtualboxVersion(): RemoteCommandOutput
    {
        return $this->vboxCommand('--version');
    }

    /**
     * List all virtual machines.
     *
     * @return RemoteCommandOutput
     */
    public function listVMs(): RemoteCommandOutput
    {
        return $this->vboxCommand('list vms');
    }

    /**
     * List running virtual machines.
     *
     * @return RemoteCommandOutput
     */
    public function listRunningVMs(): RemoteCommandOutput
    {
        return $this->vboxCommand('list runningvms');
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
        return $this->vboxCommand('showvminfo ' . $vmName . ' --machinereadable');
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
        return $this->vboxCommand('showvminfo ' . $vmName);
    }

    /**
     * Start a virtual machine.
     *
     * @param string $vmName
     * @param string $type Startup type: 'gui', 'headless', 'sdl'
     * @return RemoteCommandOutput
     */
    public function startVm(string $vmName, string $type = 'headless'): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $type = self::escapeShellArgument($type);
        return $this->vboxCommand('startvm ' . $vmName . ' --type ' . $type);
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
        return $this->vboxCommand('controlvm ' . $vmName . ' acpipowerbutton');
    }

    /**
     * Force stop (power off) a virtual machine.
     *
     * @param string $vmName
     * @return RemoteCommandOutput
     */
    public function powerOffVm(string $vmName): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        return $this->vboxCommand('controlvm ' . $vmName . ' poweroff');
    }

    /**
     * Delete (unregister) a virtual machine.
     *
     * @param string $vmName
     * @param bool $deleteDisks Also delete associated disk files
     * @return RemoteCommandOutput
     */
    public function deleteVm(string $vmName, bool $deleteDisks = false): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $diskOption = $deleteDisks ? ' --delete' : '';
        return $this->vboxCommand('unregistervm ' . $vmName . $diskOption);
    }

    /**
     * Create a virtual machine.
     *
     * @param string $vmName
     * @param string $osType OS type identifier (e.g., 'Ubuntu_64', 'Windows10_64')
     * @param string $memory Memory in MB
     * @param string $vcpu Number of vCPUs
     * @return RemoteCommandOutput
     */
    public function createVm(string $vmName, string $osType, string $memory, string $vcpu): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $osType = self::escapeShellArgument($osType);
        $memory = self::escapeShellArgument($memory);
        $vcpu = self::escapeShellArgument($vcpu);

        return $this->vboxCommand('createvm --name ' . $vmName .
            ' --ostype ' . $osType .
            ' --memory ' . $memory .
            ' --cpus ' . $vcpu .
            ' --register');
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
        return $this->vboxCommand('modifyvm ' . $vmName . ' --cpus ' . $vcpu);
    }

    /**
     * Set the memory (in MB) for a virtual machine.
     *
     * @param string $vmName
     * @param string $memory Memory in MB
     * @return RemoteCommandOutput
     */
    public function setVmMemory(string $vmName, string $memory): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $memory = self::escapeShellArgument($memory);
        return $this->vboxCommand('modifyvm ' . $vmName . ' --memory ' . $memory);
    }

    /**
     * Create and attach a virtual hard disk.
     *
     * @param string $vmName
     * @param string $diskPath Path to the disk file (e.g., /var/vbox/disk.vdi)
     * @param string $size Size in MB (for new disk creation)
     * @return RemoteCommandOutput
     */
    public function createAndAttachDisk(string $vmName, string $diskPath, string $size): RemoteCommandOutput
    {
        $vmName = self::escapeShellArgument($vmName);
        $diskPath = self::escapeShellArgument($diskPath);
        $size = self::escapeShellArgument($size);

        // Create disk
        $this->vboxCommand('createmedium disk --filename ' . $diskPath . ' --size ' . $size);

        // Attach disk
        return $this->vboxCommand('storageattach ' . $vmName .
            ' --storagectl "SATA Controller"' .
            ' --port 0 --device 0 --type hdd --medium ' . $diskPath);
    }

    /**
     * List host-only networks.
     *
     * @return RemoteCommandOutput
     */
    public function listNetworks(): RemoteCommandOutput
    {
        return $this->vboxCommand('list hostonlyifs');
    }

    /**
     * List storage controllers.
     *
     * @return RemoteCommandOutput
     */
    public function listStorageControllers(): RemoteCommandOutput
    {
        return $this->vboxCommand('list systemproperties');
    }

    /**
     * Clone a virtual machine.
     *
     * @param string $sourceVm
     * @param string $targetVm
     * @param string $mode Cloning mode: 'machine', 'machineandchildren', 'all'
     * @return RemoteCommandOutput
     */
    public function cloneVm(string $sourceVm, string $targetVm, string $mode = 'machine'): RemoteCommandOutput
    {
        $sourceVm = self::escapeShellArgument($sourceVm);
        $targetVm = self::escapeShellArgument($targetVm);
        $mode = self::escapeShellArgument($mode);

        return $this->vboxCommand('clonevm ' . $sourceVm .
            ' --name ' . $targetVm .
            ' --mode ' . $mode .
            ' --register');
    }
}
