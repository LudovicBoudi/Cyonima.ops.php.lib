<?php

declare(strict_types=1);

namespace Cyonima\Ops\Vmware;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * VMware / ESXi management operations via SSH
 *
 * Provides a direct interface to manage ESXi hosts using vim-cmd and esxcli
 * commands over SSH connections. ESXi has a limited shell environment but
 * supports these CLI tools for full virtual machine and host management.
 *
 * Example usage:
 * ```php
 * $vmware = new VmwareOps();
 * $vmware->setHost('esxi.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $vmware->openConnection();
 * $vms = $vmware->listVms();
 * $vmware->startVm('1');
 * $vmware->closeConnection();
 * ```
 */
class VmwareOps extends AbstractOps
{
    /**
     * Get the ESXi host version information
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli system version get');
    }

    /**
     * Get the ESXi hostname and domain information
     *
     * @return RemoteCommandOutput
     */
    public function getHostInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli system hostname get');
    }

    /**
     * Get the ESXi host uptime
     *
     * @return RemoteCommandOutput
     */
    public function getUptime(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli system uptime get');
    }

    /**
     * List all registered virtual machines on the host
     *
     * @return RemoteCommandOutput
     */
    public function listVms(): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/getallvms');
    }

    /**
     * Get the power state of a virtual machine
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function getVmStatus(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/power.getstate ' . self::escapeShellArgument($vmId));
    }

    /**
     * Get the full configuration details of a virtual machine
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function getVmInfo(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/get.config ' . self::escapeShellArgument($vmId));
    }

    /**
     * Power on a virtual machine
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function startVm(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/power.on ' . self::escapeShellArgument($vmId));
    }

    /**
     * Power off a virtual machine (hard stop)
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function stopVm(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/power.off ' . self::escapeShellArgument($vmId));
    }

    /**
     * Shut down a virtual machine gracefully (guest OS shutdown)
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function shutdownVm(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/power.shutdown ' . self::escapeShellArgument($vmId));
    }

    /**
     * Reset (reboot) a virtual machine
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function resetVm(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/power.reset ' . self::escapeShellArgument($vmId));
    }

    /**
     * List all datastores accessible by the ESXi host
     *
     * @return RemoteCommandOutput
     */
    public function listDatastores(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli storage filesystem list');
    }

    /**
     * List network interfaces on the ESXi host
     *
     * @return RemoteCommandOutput
     */
    public function listNetworks(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli network ip interface list');
    }

    /**
     * Get IPv4 address information for all network interfaces
     *
     * @return RemoteCommandOutput
     */
    public function getNetworkInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli network ip interface ipv4 get');
    }

    /**
     * Create a snapshot of a virtual machine
     *
     * @param string $vmId        Virtual machine ID (VMID)
     * @param string $name        Snapshot name
     * @param string|null $description Optional snapshot description
     * @return RemoteCommandOutput
     */
    public function createSnapshot(string $vmId, string $name, ?string $description = null): RemoteCommandOutput
    {
        $command = 'vim-cmd vmsvc/snapshot.create '
            . self::escapeShellArgument($vmId) . ' '
            . self::escapeShellArgument($name);

        if ($description !== null) {
            $command .= ' ' . self::escapeShellArgument($description);
        }

        return $this->remoteExec($command);
    }

    /**
     * List all snapshots of a virtual machine
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function listSnapshots(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/snapshot.get ' . self::escapeShellArgument($vmId));
    }

    /**
     * Revert a virtual machine to a specific snapshot
     *
     * @param string $vmId       Virtual machine ID (VMID)
     * @param int $snapshotId    Snapshot ID to revert to
     * @return RemoteCommandOutput
     */
    public function revertToSnapshot(string $vmId, int $snapshotId): RemoteCommandOutput
    {
        return $this->remoteExec(
            'vim-cmd vmsvc/snapshot.revert '
            . self::escapeShellArgument($vmId) . ' '
            . $snapshotId
        );
    }

    /**
     * Remove a specific snapshot from a virtual machine
     *
     * @param string $vmId       Virtual machine ID (VMID)
     * @param int $snapshotId    Snapshot ID to remove
     * @return RemoteCommandOutput
     */
    public function removeSnapshot(string $vmId, int $snapshotId): RemoteCommandOutput
    {
        return $this->remoteExec(
            'vim-cmd vmsvc/snapshot.remove '
            . self::escapeShellArgument($vmId) . ' '
            . $snapshotId
        );
    }

    /**
     * Remove all snapshots from a virtual machine
     *
     * @param string $vmId Virtual machine ID (VMID)
     * @return RemoteCommandOutput
     */
    public function removeAllSnapshots(string $vmId): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd vmsvc/snapshot.removeall ' . self::escapeShellArgument($vmId));
    }

    /**
     * List all resource pools on the ESXi host
     *
     * @return RemoteCommandOutput
     */
    public function listResourcePools(): RemoteCommandOutput
    {
        return $this->remoteExec('vim-cmd hostsvc/rsrc/hostlist');
    }

    /**
     * Get detailed hardware platform information
     *
     * @return RemoteCommandOutput
     */
    public function getHardwareInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli hardware platform get');
    }

    /**
     * List all storage devices attached to the ESXi host
     *
     * @return RemoteCommandOutput
     */
    public function getStorageDevices(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli storage core device list');
    }

    /**
     * List running virtual machine processes on the host
     *
     * Alternative way to list VMs that are currently running.
     *
     * @return RemoteCommandOutput
     */
    public function listVmHosts(): RemoteCommandOutput
    {
        return $this->remoteExec('esxcli vm process list');
    }
}
