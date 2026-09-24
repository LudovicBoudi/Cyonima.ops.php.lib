<?php

declare(strict_types=1);

namespace Cyonima\Ops\System;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * System inventory / facts gathering module
 *
 * Provides methods to collect detailed system information from remote hosts
 * via SSH. All methods return structured RemoteCommandOutput objects.
 */
class SystemOps extends AbstractOps
{
    /**
     * Get CPU information
     *
     * @return RemoteCommandOutput
     */
    public function getCpuInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('cat /proc/cpuinfo');
    }

    /**
     * Get memory usage information
     *
     * @return RemoteCommandOutput
     */
    public function getMemoryInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('free -m');
    }

    /**
     * Get disk usage overview
     *
     * @return RemoteCommandOutput
     */
    public function getDiskUsage(): RemoteCommandOutput
    {
        return $this->remoteExec('df -h');
    }

    /**
     * Get OS release information
     *
     * @return RemoteCommandOutput
     */
    public function getOsInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('cat /etc/os-release 2>/dev/null || cat /etc/*release 2>/dev/null');
    }

    /**
     * Get the kernel version
     *
     * @return RemoteCommandOutput
     */
    public function getKernelVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('uname -r');
    }

    /**
     * Get system uptime
     *
     * @return RemoteCommandOutput
     */
    public function getUptime(): RemoteCommandOutput
    {
        return $this->remoteExec('uptime');
    }

    /**
     * Get the system hostname
     *
     * @return RemoteCommandOutput
     */
    public function getHostname(): RemoteCommandOutput
    {
        return $this->remoteExec('hostname');
    }

    /**
     * List running systemd services
     *
     * @return RemoteCommandOutput
     */
    public function getRunningServices(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl list-units --type=service --state=running --no-pager');
    }

    /**
     * Get installed packages
     *
     * @param int|null $limit Optional limit to the number of packages returned
     * @return RemoteCommandOutput
     */
    public function getInstalledPackages(?int $limit = null): RemoteCommandOutput
    {
        $command = 'dpkg -l 2>/dev/null || rpm -qa 2>/dev/null';
        if ($limit !== null) {
            $command .= ' | tail -n ' . $limit;
        }
        return $this->remoteExec($command);
    }

    /**
     * Get network connections
     *
     * @return RemoteCommandOutput
     */
    public function getNetworkConnections(): RemoteCommandOutput
    {
        return $this->remoteExec('ss -tuln');
    }

    /**
     * Get currently logged-in users
     *
     * @return RemoteCommandOutput
     */
    public function getLoggedInUsers(): RemoteCommandOutput
    {
        return $this->remoteExec('who');
    }

    /**
     * Perform a DNS resolution for a hostname
     *
     * @param string $hostname The hostname to resolve
     * @return RemoteCommandOutput
     */
    public function getDnsResolution(string $hostname): RemoteCommandOutput
    {
        $escapedHostname = self::escapeShellArgument($hostname);
        return $this->remoteExec(
            'dig ' . $escapedHostname . ' +short 2>/dev/null || nslookup ' . $escapedHostname . ' 2>/dev/null'
        );
    }

    /**
     * Get the process list, optionally filtered
     *
     * @param string|null $filter Optional grep filter for the process list
     * @return RemoteCommandOutput
     */
    public function getProcessList(?string $filter = null): RemoteCommandOutput
    {
        $command = 'ps aux';
        if ($filter !== null) {
            $command .= ' | grep ' . self::escapeShellArgument($filter);
        }
        return $this->remoteExec($command);
    }

    /**
     * Get disk usage for a specific directory
     *
     * @param string $path Path to the directory
     * @return RemoteCommandOutput
     */
    public function getDiskUsageByDirectory(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('du -sh ' . self::escapeShellArgument($path) . '/*');
    }

    /**
     * Get the filesystem type for a given path
     *
     * @param string $path Path to check (default: /)
     * @return RemoteCommandOutput
     */
    public function getFileSystemType(string $path = '/'): RemoteCommandOutput
    {
        return $this->remoteExec('df -T ' . self::escapeShellArgument($path) . ' | tail -1');
    }

    /**
     * Get environment variables
     *
     * @return RemoteCommandOutput
     */
    public function getEnvironmentVariables(): RemoteCommandOutput
    {
        return $this->remoteExec('env');
    }

    /**
     * Get listening TCP ports
     *
     * @return RemoteCommandOutput
     */
    public function getListeningPorts(): RemoteCommandOutput
    {
        return $this->remoteExec('ss -tlnp');
    }

    /**
     * Get partition table information
     *
     * @return RemoteCommandOutput
     */
    public function getPartitionTable(): RemoteCommandOutput
    {
        return $this->remoteExec('lsblk -o NAME,SIZE,TYPE,MOUNTPOINT,FSTYPE');
    }

    /**
     * Get Docker daemon version information
     *
     * @return RemoteCommandOutput
     */
    public function getDockerInfo(): RemoteCommandOutput
    {
        return $this->remoteExec("docker info --format '{{.ServerVersion}}' 2>&1");
    }

    /**
     * Get system load average
     *
     * @return RemoteCommandOutput
     */
    public function getLoadAverage(): RemoteCommandOutput
    {
        return $this->remoteExec('cat /proc/loadavg');
    }
}
