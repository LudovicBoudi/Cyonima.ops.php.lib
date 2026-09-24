<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Check Point Gaia OS network equipment operations
 *
 * Provides methods for configuring Check Point firewalls and management
 * servers via SSH using the Gaia CLI syntax.
 *
 * Gaia CLI uses `set`/`delete` for configuration, `show` for display,
 * and `save config` / `reboot` / `shutdown` for operational tasks.
 * There is no separate configuration mode — commands are issued directly.
 *
 * @see https://support.checkpoint.com/research/smartconsole
 */
class CheckPointOps extends AbstractOps
{
    /**
     * Get system version information
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('show version all');
    }

    /**
     * Get asset / hardware information
     *
     * @return RemoteCommandOutput
     */
    public function getAssetInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('show asset all');
    }

    /**
     * Get the device hostname
     *
     * @return RemoteCommandOutput
     */
    public function getHostname(): RemoteCommandOutput
    {
        return $this->remoteExec('show configuration hostname');
    }

    /**
     * Set the device hostname
     *
     * @param string $hostname New hostname
     * @return RemoteCommandOutput
     */
    public function setHostname(string $hostname): RemoteCommandOutput
    {
        $cmd = 'set hostname ' . self::escapeShellArgument($hostname) . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Get system time and date
     *
     * @return RemoteCommandOutput
     */
    public function getTime(): RemoteCommandOutput
    {
        return $this->remoteExec('show time');
    }

    /**
     * Get NTP configuration and status
     *
     * @return RemoteCommandOutput
     */
    public function getNtpStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('show ntp');
    }

    /**
     * Configure NTP server
     *
     * @param string $server NTP server IP or hostname
     * @return RemoteCommandOutput
     */
    public function setNtpServer(string $server): RemoteCommandOutput
    {
        $cmd = 'set ntp server ' . self::escapeShellArgument($server)
            . ' && set ntp active on'
            . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Get DNS configuration
     *
     * @return RemoteCommandOutput
     */
    public function getDnsConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('show dns');
    }

    /**
     * Configure DNS servers
     *
     * @param string $primary Primary DNS server IP
     * @param string|null $secondary Secondary DNS server IP
     * @return RemoteCommandOutput
     */
    public function setDnsServers(string $primary, ?string $secondary = null): RemoteCommandOutput
    {
        $cmd = 'set dns primary ' . self::escapeShellArgument($primary);
        if ($secondary !== null) {
            $cmd .= ' && set dns secondary ' . self::escapeShellArgument($secondary);
        }
        $cmd .= ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Reboot the device
     *
     * @return RemoteCommandOutput
     */
    public function reboot(): RemoteCommandOutput
    {
        return $this->remoteExec('reboot');
    }

    /**
     * Shutdown the device
     *
     * @return RemoteCommandOutput
     */
    public function shutdown(): RemoteCommandOutput
    {
        return $this->remoteExec('shutdown');
    }

    /**
     * Save the current configuration to persistent storage
     *
     * @return RemoteCommandOutput
     */
    public function saveConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('save config');
    }

    /**
     * Get all interfaces (brief)
     *
     * @return RemoteCommandOutput
     */
    public function getInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('show interface all');
    }

    /**
     * Get detailed information for a specific interface
     *
     * @param string $interface Interface name (e.g. "eth0", "bond0")
     * @return RemoteCommandOutput
     */
    public function getInterfaceDetail(string $interface): RemoteCommandOutput
    {
        $cmd = 'show interface ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Set IP address on an interface
     *
     * @param string $interface Interface name
     * @param string $ipAddress IP address
     * @param int $maskLength Subnet mask length (e.g. 24)
     * @return RemoteCommandOutput
     */
    public function setInterfaceIp(string $interface, string $ipAddress, int $maskLength): RemoteCommandOutput
    {
        $cmd = 'set interface ' . self::escapeShellArgument($interface)
            . ' ipv4-address ' . self::escapeShellArgument($ipAddress)
            . ' mask-length ' . $maskLength
            . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable an interface (administratively up)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function enableInterface(string $interface): RemoteCommandOutput
    {
        $cmd = 'set interface ' . self::escapeShellArgument($interface) . ' state on && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable an interface (administratively down)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function disableInterface(string $interface): RemoteCommandOutput
    {
        $cmd = 'set interface ' . self::escapeShellArgument($interface) . ' state off && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Set a comment on an interface
     *
     * @param string $interface Interface name
     * @param string $comment Comment text
     * @return RemoteCommandOutput
     */
    public function setInterfaceComment(string $interface, string $comment): RemoteCommandOutput
    {
        $cmd = 'set interface ' . self::escapeShellArgument($interface)
            . ' comments ' . self::escapeShellArgument($comment)
            . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Set interface MTU
     *
     * @param string $interface Interface name
     * @param int $mtu MTU value (e.g. 1500)
     * @return RemoteCommandOutput
     */
    public function setInterfaceMtu(string $interface, int $mtu): RemoteCommandOutput
    {
        $cmd = 'set interface ' . self::escapeShellArgument($interface)
            . ' mtu ' . $mtu
            . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Get the routing table
     *
     * @return RemoteCommandOutput
     */
    public function getRoutes(): RemoteCommandOutput
    {
        return $this->remoteExec('show route');
    }

    /**
     * Get the ARP table
     *
     * @return RemoteCommandOutput
     */
    public function getArpTable(): RemoteCommandOutput
    {
        return $this->remoteExec('show arp all');
    }

    /**
     * Add a static route
     *
     * @param string $prefix Destination prefix in CIDR (e.g. "10.0.0.0/8")
     * @param string $gateway Gateway IP address
     * @param int|null $priority Optional route priority
     * @return RemoteCommandOutput
     */
    public function addStaticRoute(string $prefix, string $gateway, ?int $priority = null): RemoteCommandOutput
    {
        $cmd = 'set static-route ' . self::escapeShellArgument($prefix)
            . ' nexthop gateway address ' . self::escapeShellArgument($gateway) . ' on';

        if ($priority !== null) {
            $cmd .= ' priority ' . $priority;
        }

        $cmd .= ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a static route
     *
     * @param string $prefix Destination prefix in CIDR
     * @return RemoteCommandOutput
     */
    public function removeStaticRoute(string $prefix): RemoteCommandOutput
    {
        $cmd = 'delete static-route ' . self::escapeShellArgument($prefix) . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Set the default gateway
     *
     * @param string $gateway Gateway IP address
     * @return RemoteCommandOutput
     */
    public function setDefaultGateway(string $gateway): RemoteCommandOutput
    {
        $cmd = 'set static-route default nexthop gateway address ' . self::escapeShellArgument($gateway) . ' on && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Get cluster status (if in a cluster)
     *
     * @return RemoteCommandOutput
     */
    public function getClusterStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('show cluster');
    }

    /**
     * Get Check Point firewall status (fw stat)
     *
     * @return RemoteCommandOutput
     */
    public function getFirewallStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('show firewall all');
    }

    /**
     * Get all admin users
     *
     * @return RemoteCommandOutput
     */
    public function getUsers(): RemoteCommandOutput
    {
        return $this->remoteExec('show user all');
    }

    /**
     * Add a local admin user
     *
     * @param string $username Username
     * @param string $password Password
     * @return RemoteCommandOutput
     */
    public function createUser(string $username, string $password): RemoteCommandOutput
    {
        $cmd = 'add user ' . self::escapeShellArgument($username)
            . ' && set user ' . self::escapeShellArgument($username)
            . ' password ' . self::escapeShellArgument($password)
            . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a local admin user
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        $cmd = 'delete user ' . self::escapeShellArgument($username) . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Get SNMP community configuration
     *
     * @return RemoteCommandOutput
     */
    public function getSnmpCommunities(): RemoteCommandOutput
    {
        return $this->remoteExec('show snmp community all');
    }

    /**
     * Add an SNMP community string
     *
     * @param string $name Community name
     * @return RemoteCommandOutput
     */
    public function addSnmpCommunity(string $name): RemoteCommandOutput
    {
        $cmd = 'set snmp community ' . self::escapeShellArgument($name) . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove an SNMP community string
     *
     * @param string $name Community name
     * @return RemoteCommandOutput
     */
    public function deleteSnmpCommunity(string $name): RemoteCommandOutput
    {
        $cmd = 'delete snmp community ' . self::escapeShellArgument($name) . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable or disable the SNMP agent
     *
     * @param bool $enable Whether to enable the SNMP agent
     * @return RemoteCommandOutput
     */
    public function setSnmpAgent(bool $enable): RemoteCommandOutput
    {
        $cmd = 'set snmp agent ' . ($enable ? 'on' : 'off') . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Get syslog server configuration
     *
     * @return RemoteCommandOutput
     */
    public function getSyslogConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('show syslog all');
    }

    /**
     * Configure remote syslog server
     *
     * @param string $server Syslog server IP
     * @param string $facility Syslog facility (local0-local7)
     * @return RemoteCommandOutput
    */
    public function setSyslogServer(string $server, string $facility = 'local0'): RemoteCommandOutput
    {
        $cmd = 'set syslog server ' . self::escapeShellArgument($server)
            . ' && set syslog server ' . self::escapeShellArgument($server)
            . ' facility ' . self::escapeShellArgument($facility)
            . ' && save config';
        return $this->remoteExec($cmd);
    }

    /**
     * Get running tasks
     *
     * @return RemoteCommandOutput
     */
    public function getTasks(): RemoteCommandOutput
    {
        return $this->remoteExec('show task all');
    }

    /**
     * Get recent log entries (last N lines)
     *
     * Note: In expert mode, use exec('tail -100 $FWDIR/log/fw.log') instead.
     *
     * @param int $lines Number of lines (default 100)
     * @return RemoteCommandOutput
     */
    public function getLogs(int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('show log last ' . $lines);
    }

    /**
     * Get Security Gateway version / blade status
     *
     * @return RemoteCommandOutput
     */
    public function getGatewayStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('show gateway all');
    }

    /**
     * Ping a remote host
     *
     * @param string $host IP address or hostname
     * @param int $count Number of packets (default 5)
     * @param int $size Packet size (default 64)
     * @return RemoteCommandOutput
     */
    public function ping(string $host, int $count = 5, int $size = 64): RemoteCommandOutput
    {
        $cmd = 'ping ' . self::escapeShellArgument($host) . ' count ' . $count . ' size ' . $size;
        return $this->remoteExec($cmd);
    }

    /**
     * Traceroute to a remote host
     *
     * @param string $host IP address or hostname
     * @return RemoteCommandOutput
     */
    public function traceroute(string $host): RemoteCommandOutput
    {
        return $this->remoteExec('traceroute ' . self::escapeShellArgument($host));
    }

    /**
     * Get the full running configuration
     *
     * @return RemoteCommandOutput
     */
    public function getConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('show configuration');
    }

    /**
     * Get license information
     *
     * @return RemoteCommandOutput
     */
    public function getLicenses(): RemoteCommandOutput
    {
        return $this->remoteExec('show license all');
    }

    /**
     * Execute an arbitrary Gaia CLI command
     *
     * @param string $command Any Gaia CLI command
     * @return RemoteCommandOutput
     */
    public function exec(string $command): RemoteCommandOutput
    {
        return $this->remoteExec($command);
    }
}
