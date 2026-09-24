<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Juniper JunOS network equipment operations
 *
 * Provides methods for configuring Juniper switches and routers
 * via SSH using JunOS CLI syntax (set/delete/commit pattern).
 */
class JuniperOps extends AbstractOps
{
    /**
     * Get the device hostname
     *
     * @return RemoteCommandOutput
     */
    public function getHostname(): RemoteCommandOutput
    {
        return $this->remoteExec('hostname');
    }

    /**
     * Get JunOS version information
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('show version');
    }

    /**
     * Get system uptime
     *
     * @return RemoteCommandOutput
     */
    public function getUptime(): RemoteCommandOutput
    {
        return $this->remoteExec('show system uptime');
    }

    /**
     * Get the full running configuration
     *
     * @return RemoteCommandOutput
     */
    public function getConfiguration(): RemoteCommandOutput
    {
        return $this->remoteExec('show configuration');
    }

    /**
     * Get configuration for a specific section
     *
     * @param string $section Configuration section (e.g. "interfaces", "system", "routing-options")
     * @return RemoteCommandOutput
     */
    public function getConfigurationSection(string $section): RemoteCommandOutput
    {
        $cmd = 'show configuration ' . self::escapeShellArgument($section);
        return $this->remoteExec($cmd);
    }

    /**
     * Get all interfaces status (brief)
     *
     * @return RemoteCommandOutput
     */
    public function getInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('show interfaces terse');
    }

    /**
     * Get detailed information for a specific interface
     *
     * @param string $interface Interface name (e.g. "ge-0/0/0", "xe-0/0/0")
     * @return RemoteCommandOutput
     */
    public function getInterfaceDetail(string $interface): RemoteCommandOutput
    {
        $cmd = 'show interfaces ' . self::escapeShellArgument($interface) . ' extensive';
        return $this->remoteExec($cmd);
    }

    /**
     * Get interface status (up/down)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function getInterfaceStatus(string $interface): RemoteCommandOutput
    {
        $cmd = 'show interfaces ' . self::escapeShellArgument($interface) . ' terse';
        return $this->remoteExec($cmd);
    }

    /**
     * Get the routing table
     *
     * @return RemoteCommandOutput
     */
    public function getRouteTable(): RemoteCommandOutput
    {
        return $this->remoteExec('show route');
    }

    /**
     * Get route for a specific prefix
     *
     * @param string $prefix IP prefix (e.g. "10.0.0.0/8")
     * @return RemoteCommandOutput
     */
    public function getRouteForPrefix(string $prefix): RemoteCommandOutput
    {
        $cmd = 'show route ' . self::escapeShellArgument($prefix);
        return $this->remoteExec($cmd);
    }

    /**
     * Get the ARP table
     *
     * @return RemoteCommandOutput
     */
    public function getArpTable(): RemoteCommandOutput
    {
        return $this->remoteExec('show arp');
    }

    /**
     * Get the MAC address table (for switches)
     *
     * @return RemoteCommandOutput
     */
    public function getMacTable(): RemoteCommandOutput
    {
        return $this->remoteExec('show ethernet-switching table');
    }

    /**
     * Get MAC table for a specific VLAN
     *
     * @param string $vlanName VLAN name
     * @return RemoteCommandOutput
     */
    public function getMacTableForVlan(string $vlanName): RemoteCommandOutput
    {
        $cmd = 'show ethernet-switching table vlan ' . self::escapeShellArgument($vlanName);
        return $this->remoteExec($cmd);
    }

    /**
     * Get VLAN information
     *
     * @return RemoteCommandOutput
     */
    public function getVlans(): RemoteCommandOutput
    {
        return $this->remoteExec('show vlans');
    }

    /**
     * Get LLDP neighbors
     *
     * @return RemoteCommandOutput
     */
    public function getLldpNeighbors(): RemoteCommandOutput
    {
        return $this->remoteExec('show lldp neighbors');
    }

    /**
     * Get log messages
     *
     * @param int $lines Number of lines to retrieve
     * @return RemoteCommandOutput
     */
    public function getLogMessages(int $lines = 50): RemoteCommandOutput
    {
        $cmd = 'show log messages | tail ' . $lines;
        return $this->remoteExec($cmd);
    }

    /**
     * Ping a host from the Juniper device
     *
     * @param string $host IP address or hostname to ping
     * @param int $count Number of ping packets
     * @return RemoteCommandOutput
     */
    public function ping(string $host, int $count = 5): RemoteCommandOutput
    {
        $cmd = 'ping ' . self::escapeShellArgument($host) . ' count ' . $count . ' rapid';
        return $this->remoteExec($cmd);
    }

    /**
     * Traceroute from the Juniper device
     *
     * @param string $host IP address or hostname
     * @return RemoteCommandOutput
     */
    public function traceroute(string $host): RemoteCommandOutput
    {
        $cmd = 'traceroute ' . self::escapeShellArgument($host);
        return $this->remoteExec($cmd);
    }

    /**
     * Monitor interface traffic in real-time (runs until Ctrl-C)
     *
     * @param string $interface Interface name
     * @param int $interval Refresh interval in seconds
     * @return RemoteCommandOutput
     */
    public function monitorInterface(string $interface, int $interval = 2): RemoteCommandOutput
    {
        $cmd = 'monitor interface ' . self::escapeShellArgument($interface) . ' interval ' . $interval;
        return $this->remoteExec($cmd);
    }

    /**
     * Set the device hostname
     *
     * @param string $hostname New hostname
     * @return RemoteCommandOutput
     */
    public function setHostname(string $hostname): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set system host-name ' . self::escapeShellArgument($hostname)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a VLAN
     *
     * @param string $vlanId VLAN ID (1-4094)
     * @param string $vlanName VLAN name
     * @param string|null $vlanInterface Optional layer-3 interface (e.g. "vlan.100")
     * @param string|null $ipAddress Optional IP address with prefix (e.g. "10.0.0.1/24")
     * @return RemoteCommandOutput
     */
    public function createVlan(string $vlanId, string $vlanName, ?string $vlanInterface = null, ?string $ipAddress = null): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set vlans ' . self::escapeShellArgument($vlanName) . ' vlan-id ' . $vlanId;

        if ($vlanInterface !== null) {
            $cmd .= ' && set interfaces ' . self::escapeShellArgument($vlanInterface) . ' unit 0 family inet';

            if ($ipAddress !== null) {
                $cmd .= ' address ' . self::escapeShellArgument($ipAddress);
            }

            $cmd .= ' && set vlans ' . self::escapeShellArgument($vlanName) . ' l3-interface ' . self::escapeShellArgument($vlanInterface);
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a VLAN
     *
     * @param string $vlanName VLAN name
     * @return RemoteCommandOutput
     */
    public function deleteVlan(string $vlanName): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete vlans ' . self::escapeShellArgument($vlanName)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Assign an interface to a VLAN (access mode)
     *
     * @param string $interface Interface name (e.g. "ge-0/0/1")
     * @param string $vlanName VLAN name
     * @return RemoteCommandOutput
     */
    public function setInterfaceAccessVlan(string $interface, string $vlanName): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' unit 0 family ethernet-switching interface-mode access'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' unit 0 family ethernet-switching vlan members ' . self::escapeShellArgument($vlanName)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure an interface as trunk port
     *
     * @param string $interface Interface name
     * @param array<string> $allowedVlans List of allowed VLAN names
     * @return RemoteCommandOutput
     */
    public function setInterfaceTrunk(string $interface, array $allowedVlans): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' unit 0 family ethernet-switching interface-mode trunk';

        foreach ($allowedVlans as $vlan) {
            $cmd .= ' && set interfaces ' . self::escapeShellArgument($interface) . ' unit 0 family ethernet-switching vlan members ' . self::escapeShellArgument($vlan);
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure an interface description
     *
     * @param string $interface Interface name
     * @param string $description Description text
     * @return RemoteCommandOutput
     */
    public function setInterfaceDescription(string $interface, string $description): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' description ' . self::escapeShellArgument($description)
            . ' && commit';
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
        $cmd = 'configure'
            . ' && delete interfaces ' . self::escapeShellArgument($interface) . ' unit 0 family inet'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' disable'
            . ' && commit';
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
        $cmd = 'configure'
            . ' && delete interfaces ' . self::escapeShellArgument($interface) . ' disable'
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Set IP address on an interface
     *
     * @param string $interface Interface name (e.g. "ge-0/0/0")
     * @param string $ipAddress IP address with prefix (e.g. "10.0.0.1/24")
     * @return RemoteCommandOutput
     */
    public function setInterfaceIp(string $interface, string $ipAddress): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' unit 0 family inet address ' . self::escapeShellArgument($ipAddress)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove IP address from an interface
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function removeInterfaceIp(string $interface): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete interfaces ' . self::escapeShellArgument($interface) . ' unit 0 family inet address'
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Add a static route
     *
     * @param string $destination Destination prefix (e.g. "10.0.0.0/8")
     * @param string $nextHop Next-hop IP address
     * @param int|null $preference Optional route preference (administrative distance)
     * @return RemoteCommandOutput
     */
    public function addStaticRoute(string $destination, string $nextHop, ?int $preference = null): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set routing-options static route ' . self::escapeShellArgument($destination) . ' next-hop ' . self::escapeShellArgument($nextHop);

        if ($preference !== null) {
            $cmd .= ' preference ' . $preference;
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a static route
     *
     * @param string $destination Destination prefix
     * @return RemoteCommandOutput
     */
    public function removeStaticRoute(string $destination): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete routing-options static route ' . self::escapeShellArgument($destination)
            . ' && commit';
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
        return $this->addStaticRoute('0.0.0.0/0', $gateway);
    }

    /**
     * Create a local user account
     *
     * @param string $username Username
     * @param string $password Plain-text password (will be prompted by JunOS)
     * @param string $class User class (super-user, read-only, operator, etc.)
     * @return RemoteCommandOutput
     */
    public function createUser(string $username, string $password, string $class = 'super-user'): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set system login user ' . self::escapeShellArgument($username) . ' class ' . self::escapeShellArgument($class)
            . ' && set system login user ' . self::escapeShellArgument($username) . ' authentication plain-text-password-value ' . self::escapeShellArgument($password)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a user account
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete system login user ' . self::escapeShellArgument($username)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Add a firewall filter (ACL)
     *
     * @param string $filterName Filter name
     * @param string $termName Term name
     * @param string $action Action (accept, reject, discard)
     * @param string|null $sourceAddress Optional source IP with prefix
     * @param string|null $destinationAddress Optional destination IP with prefix
     * @param string|null $protocol Optional protocol (tcp, udp, icmp)
     * @param int|null $sourcePort Optional source port
     * @param int|null $destinationPort Optional destination port
     * @return RemoteCommandOutput
     */
    public function addFirewallFilter(
        string $filterName,
        string $termName,
        string $action,
        ?string $sourceAddress = null,
        ?string $destinationAddress = null,
        ?string $protocol = null,
        ?int $sourcePort = null,
        ?int $destinationPort = null
    ): RemoteCommandOutput {
        $cmd = 'configure'
            . ' && set firewall family inet filter ' . self::escapeShellArgument($filterName)
            . ' term ' . self::escapeShellArgument($termName) . ' then ' . $action;

        if ($sourceAddress !== null) {
            $cmd .= ' && set firewall family inet filter ' . self::escapeShellArgument($filterName)
                . ' term ' . self::escapeShellArgument($termName)
                . ' from source-address ' . self::escapeShellArgument($sourceAddress);
        }

        if ($destinationAddress !== null) {
            $cmd .= ' && set firewall family inet filter ' . self::escapeShellArgument($filterName)
                . ' term ' . self::escapeShellArgument($termName)
                . ' from destination-address ' . self::escapeShellArgument($destinationAddress);
        }

        if ($protocol !== null) {
            $cmd .= ' && set firewall family inet filter ' . self::escapeShellArgument($filterName)
                . ' term ' . self::escapeShellArgument($termName)
                . ' from protocol ' . $protocol;
        }

        if ($sourcePort !== null) {
            $cmd .= ' && set firewall family inet filter ' . self::escapeShellArgument($filterName)
                . ' term ' . self::escapeShellArgument($termName)
                . ' from source-port ' . $sourcePort;
        }

        if ($destinationPort !== null) {
            $cmd .= ' && set firewall family inet filter ' . self::escapeShellArgument($filterName)
                . ' term ' . self::escapeShellArgument($termName)
                . ' from destination-port ' . $destinationPort;
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a firewall filter
     *
     * @param string $filterName Filter name
     * @return RemoteCommandOutput
     */
    public function deleteFirewallFilter(string $filterName): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete firewall family inet filter ' . self::escapeShellArgument($filterName)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Apply a firewall filter to an interface (input)
     *
     * @param string $interface Interface name
     * @param string $filterName Filter name
     * @return RemoteCommandOutput
     */
    public function applyFilterInput(string $interface, string $filterName): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface)
            . ' unit 0 family inet filter input ' . self::escapeShellArgument($filterName)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Apply a firewall filter to an interface (output)
     *
     * @param string $interface Interface name
     * @param string $filterName Filter name
     * @return RemoteCommandOutput
     */
    public function applyFilterOutput(string $interface, string $filterName): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface)
            . ' unit 0 family inet filter output ' . self::escapeShellArgument($filterName)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure NTP servers
     *
     * @param array<string> $servers List of NTP server hostnames/IPs
     * @return RemoteCommandOutput
     */
    public function setNtpServers(array $servers): RemoteCommandOutput
    {
        $cmd = 'configure';
        foreach ($servers as $server) {
            $cmd .= ' && set system ntp server ' . self::escapeShellArgument($server);
        }
        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure SNMP community
     *
     * @param string $community SNMP community string
     * @param string $access Access level (read-only, read-write)
     * @return RemoteCommandOutput
     */
    public function setSnmpCommunity(string $community, string $access = 'read-only'): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set snmp community ' . self::escapeShellArgument($community) . ' authorization ' . self::escapeShellArgument($access)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure syslog server
     *
     * @param string $server Syslog server IP
     * @param string $facility Syslog facility (e.g. "any")
     * @param string $level Log level (e.g. "info", "warning", "error")
     * @return RemoteCommandOutput
     */
    public function setSyslogServer(string $server, string $facility = 'any', string $level = 'info'): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set system syslog host ' . self::escapeShellArgument($server)
            . ' facility ' . self::escapeShellArgument($facility) . ' ' . self::escapeShellArgument($level)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure interface MTU
     *
     * @param string $interface Interface name
     * @param int $mtu MTU value
     * @return RemoteCommandOutput
     */
    public function setInterfaceMtu(string $interface, int $mtu): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' mtu ' . $mtu
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure interface speed
     *
     * @param string $interface Interface name
     * @param string $speed Speed (e.g. "auto", "100m", "1g", "10g")
     * @return RemoteCommandOutput
     */
    public function setInterfaceSpeed(string $interface, string $speed): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set interfaces ' . self::escapeShellArgument($interface) . ' speed ' . $speed
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure LLDP (enable globally)
     *
     * @return RemoteCommandOutput
     */
    public function enableLldp(): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set protocols lldp interface all'
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure spanning-tree (RSTP)
     *
     * @return RemoteCommandOutput
     */
    public function enableRstp(): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set protocols rstp interface all'
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure DHCP relay
     *
     * @param string $serverGroup DHCP server group name
     * @param array<string> $serverIps List of DHCP server IPs
     * @return RemoteCommandOutput
     */
    public function setDhcpRelay(string $serverGroup, array $serverIps): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set forwarding-options dhcp-relay server-group ' . self::escapeShellArgument($serverGroup);

        foreach ($serverIps as $ip) {
            $cmd .= ' ' . self::escapeShellArgument($ip);
        }

        $cmd .= ' && set forwarding-options dhcp-relay active-server-group ' . self::escapeShellArgument($serverGroup)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Commit the candidate configuration
     *
     * @return RemoteCommandOutput
     */
    public function commit(): RemoteCommandOutput
    {
        return $this->remoteExec('commit');
    }

    /**
     * Validate the candidate configuration without applying it
     *
     * @return RemoteCommandOutput
     */
    public function commitCheck(): RemoteCommandOutput
    {
        return $this->remoteExec('commit check');
    }

    /**
     * Rollback configuration to a previous revision
     *
     * @param int $revision Revision number (0 = most recent commit)
     * @return RemoteCommandOutput
     */
    public function rollback(int $revision = 0): RemoteCommandOutput
    {
        $cmd = 'rollback ' . $revision . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Show configuration changes since last commit
     *
     * @return RemoteCommandOutput
     */
    public function showChanges(): RemoteCommandOutput
    {
        return $this->remoteExec('show | compare');
    }

    /**
     * Save the current rescue configuration
     *
     * @return RemoteCommandOutput
     */
    public function saveRescueConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('request system configuration rescue save');
    }

    /**
     * Reboot the device
     *
     * @param int $delayInMinutes Delay before reboot (0 = immediate)
     * @return RemoteCommandOutput
     */
    public function reboot(int $delayInMinutes = 0): RemoteCommandOutput
    {
        $cmd = 'request system reboot';
        if ($delayInMinutes > 0) {
            $cmd .= ' at ' . $delayInMinutes;
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Apply a configuration script (set commands) in one transaction
     *
     * @param array<string> $setCommands List of set commands
     * @return RemoteCommandOutput
     */
    public function applyConfigScript(array $setCommands): RemoteCommandOutput
    {
        $parts = ['configure'];
        foreach ($setCommands as $command) {
            $parts[] = 'set ' . $command;
        }
        $parts[] = 'commit';
        return $this->remoteExec(implode(' && ', $parts));
    }

    /**
     * Get interface diagnostics (optical transceiver info)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function getInterfaceDiagnostics(string $interface): RemoteCommandOutput
    {
        $cmd = 'show interface diagnostics optics ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Get active alarms
     *
     * @return RemoteCommandOutput
     */
    public function getAlarms(): RemoteCommandOutput
    {
        return $this->remoteExec('show system alarms');
    }

    /**
     * Get active users logged into the device
     *
     * @return RemoteCommandOutput
     */
    public function getActiveUsers(): RemoteCommandOutput
    {
        return $this->remoteExec('show system users');
    }

    /**
     * Execute an arbitrary operational mode command
     *
     * @param string $operationalCommand Any JunOS operational mode command
     * @return RemoteCommandOutput
     */
    public function execOperational(string $operationalCommand): RemoteCommandOutput
    {
        return $this->remoteExec($operationalCommand);
    }
}
