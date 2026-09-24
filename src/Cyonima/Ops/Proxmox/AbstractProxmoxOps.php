<?php

declare(strict_types=1);

namespace Cyonima\Ops\Proxmox;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for Proxmox operations via Proxmox CLI/API over SSH
 *
 * Requires Proxmox VE to be installed and the `pvesh` (Proxmox VE Shell) command
 * to be available in the remote environment's PATH, or alternately `qm` for QEMU/KVM
 * management.
 */
abstract class AbstractProxmoxOps extends AbstractOps
{
    /**
     * Execute a Proxmox qm (QEMU) subcommand.
     *
     * @param string $subcommand qm subcommand
     * @return RemoteCommandOutput
     */
    protected function qmCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('qm ' . $subcommand);
    }

    /**
     * Execute a Proxmox pvesh (Proxmox VE Shell) subcommand.
     *
     * @param string $subcommand pvesh subcommand
     * @return RemoteCommandOutput
     */
    protected function pveshCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('pvesh ' . $subcommand);
    }

    /**
     * Get Proxmox version information.
     *
     * @return RemoteCommandOutput
     */
    public function getProxmoxVersion(): RemoteCommandOutput
    {
        return $this->pveshCommand('get /version');
    }

    /**
     * List all nodes in the Proxmox cluster.
     *
     * @return RemoteCommandOutput
     */
    public function listNodes(): RemoteCommandOutput
    {
        return $this->pveshCommand('get /nodes');
    }

    /**
     * List all virtual machines (LXC containers and QEMU VMs).
     *
     * @return RemoteCommandOutput
     */
    public function listVMs(): RemoteCommandOutput
    {
        return $this->qmCommand('list');
    }

    /**
     * Get the status of a specific VM.
     *
     * @param string $vmid Virtual machine ID
     * @return RemoteCommandOutput
     */
    public function getVmStatus(string $vmid): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        return $this->qmCommand('status ' . $vmid);
    }

    /**
     * Get detailed configuration of a VM.
     *
     * @param string $vmid Virtual machine ID
     * @return RemoteCommandOutput
     */
    public function getVmConfig(string $vmid): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        return $this->qmCommand('config ' . $vmid);
    }

    /**
     * Start a virtual machine.
     *
     * @param string $vmid Virtual machine ID
     * @return RemoteCommandOutput
     */
    public function startVm(string $vmid): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        return $this->qmCommand('start ' . $vmid);
    }

    /**
     * Stop (gracefully shutdown) a virtual machine.
     *
     * @param string $vmid Virtual machine ID
     * @return RemoteCommandOutput
     */
    public function stopVm(string $vmid): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        return $this->qmCommand('shutdown ' . $vmid);
    }

    /**
     * Force stop (kill) a virtual machine.
     *
     * @param string $vmid Virtual machine ID
     * @return RemoteCommandOutput
     */
    public function killVm(string $vmid): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        return $this->qmCommand('stop ' . $vmid);
    }

    /**
     * Delete (destroy) a virtual machine.
     *
     * @param string $vmid Virtual machine ID
     * @return RemoteCommandOutput
     */
    public function deleteVm(string $vmid): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        return $this->qmCommand('destroy ' . $vmid);
    }

    /**
     * Create a virtual machine.
     *
     * @param string $vmid Virtual machine ID
     * @param string $name VM name
     * @param string $memory Memory in MB
     * @param string $cores Number of CPU cores
     * @param string $storage Storage identifier
     * @return RemoteCommandOutput
     */
    public function createVm(string $vmid, string $name, string $memory, string $cores, string $storage): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        $name = self::escapeShellArgument($name);
        $memory = self::escapeShellArgument($memory);
        $cores = self::escapeShellArgument($cores);
        $storage = self::escapeShellArgument($storage);

        return $this->qmCommand('create ' . $vmid .
            ' --name ' . $name .
            ' --memory ' . $memory .
            ' --cores ' . $cores .
            ' --scsihw virtio-scsi-pci --scsi0 ' . $storage . ':0');
    }

    /**
     * Set the number of CPU cores for a VM.
     *
     * @param string $vmid Virtual machine ID
     * @param string $cores Number of cores
     * @return RemoteCommandOutput
     */
    public function setVmCpu(string $vmid, string $cores): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        $cores = self::escapeShellArgument($cores);
        return $this->qmCommand('set ' . $vmid . ' --cores ' . $cores);
    }

    /**
     * Set the memory for a VM.
     *
     * @param string $vmid Virtual machine ID
     * @param string $memory Memory in MB
     * @return RemoteCommandOutput
     */
    public function setVmMemory(string $vmid, string $memory): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        $memory = self::escapeShellArgument($memory);
        return $this->qmCommand('set ' . $vmid . ' --memory ' . $memory);
    }

    /**
     * Resize a disk attached to a VM.
     *
     * @param string $vmid Virtual machine ID
     * @param string $disk Disk identifier (e.g., 'scsi0')
     * @param string $size Size to add (e.g., '+10G')
     * @return RemoteCommandOutput
     */
    public function resizeDisk(string $vmid, string $disk, string $size): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        $disk = self::escapeShellArgument($disk);
        $size = self::escapeShellArgument($size);
        return $this->qmCommand('resize ' . $vmid . ' ' . $disk . ' ' . $size);
    }

    /**
     * Migrate a VM to another node.
     *
     * @param string $vmid Virtual machine ID
     * @param string $targetNode Target node name
     * @return RemoteCommandOutput
     */
    public function migrateVm(string $vmid, string $targetNode): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        $targetNode = self::escapeShellArgument($targetNode);
        return $this->qmCommand('migrate ' . $vmid . ' ' . $targetNode);
    }

    /**
     * List storage resources.
     *
     * @return RemoteCommandOutput
     */
    public function listStorages(): RemoteCommandOutput
    {
        return $this->pveshCommand('get /storage');
    }

    /**
     * List cluster nodes status.
     *
     * @return RemoteCommandOutput
     */
    public function getClusterStatus(): RemoteCommandOutput
    {
        return $this->pveshCommand('get /cluster/status');
    }

    /**
     * Clone a VM.
     *
     * @param string $sourceVmid Source VM ID
     * @param string $targetVmid Target VM ID
     * @param string $name Target VM name
     * @return RemoteCommandOutput
     */
    public function cloneVm(string $sourceVmid, string $targetVmid, string $name): RemoteCommandOutput
    {
        $sourceVmid = self::escapeShellArgument($sourceVmid);
        $targetVmid = self::escapeShellArgument($targetVmid);
        $name = self::escapeShellArgument($name);

        return $this->qmCommand('clone ' . $sourceVmid . ' ' . $targetVmid .
            ' --name ' . $name);
    }

    /**
     * Create a snapshot of a VM.
     *
     * @param string $vmid Virtual machine ID
     * @param string $snapname Snapshot name
     * @return RemoteCommandOutput
     */
    public function createSnapshot(string $vmid, string $snapname): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        $snapname = self::escapeShellArgument($snapname);
        return $this->qmCommand('snapshot ' . $vmid . ' --snapname ' . $snapname);
    }

    /**
     * List snapshots of a VM.
     *
     * @param string $vmid Virtual machine ID
     * @return RemoteCommandOutput
     */
    public function listSnapshots(string $vmid): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        return $this->qmCommand('listsnapshot ' . $vmid);
    }

    /**
     * Rollback a VM to a snapshot.
     *
     * @param string $vmid Virtual machine ID
     * @param string $snapname Snapshot name
     * @return RemoteCommandOutput
     */
    public function rollbackSnapshot(string $vmid, string $snapname): RemoteCommandOutput
    {
        $vmid = self::escapeShellArgument($vmid);
        $snapname = self::escapeShellArgument($snapname);
        return $this->qmCommand('rollback ' . $vmid . ' ' . $snapname);
    }
}
