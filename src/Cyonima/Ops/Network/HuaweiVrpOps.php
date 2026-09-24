<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Huawei VRP (Versatile Routing Platform) network equipment operations
 *
 * Provides methods for configuring Huawei switches and routers
 * via SSH using the VRP CLI syntax. All arguments are escaped.
 *
 * VRP uses `system-view` for config mode, `display` instead of `show`,
 * `undo` instead of `no`, and `save` instead of `write memory`.
 * Changes take effect immediately when exiting the view.
 *
 * @see https://support.huawei.com/enterprise/en/doc/EDOC1100274883
 */
class HuaweiVrpOps extends AbstractOps
{
    /**
     * Enter system view from user view
     *
     * @return RemoteCommandOutput
     */
    public function enterSystemView(): RemoteCommandOutput
    {
        return $this->remoteExec('system-view');
    }

    /**
     * Return from system view to user view
     *
     * @return RemoteCommandOutput
     */
    public function returnToUserView(): RemoteCommandOutput
    {
        return $this->remoteExec('return');
    }

    /**
     * Save the current configuration to the next startup config file
     *
     * @return RemoteCommandOutput
     */
    public function saveConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('save');
    }

    /**
     * Disable pagination for the current session
     *
     * @return RemoteCommandOutput
     */
    public function disablePagination(): RemoteCommandOutput
    {
        return $this->remoteExec('screen-length 0 temporary');
    }

    /**
     * Get the device hostname
     *
     * @return RemoteCommandOutput
     */
    public function getHostname(): RemoteCommandOutput
    {
        return $this->remoteExec('display current-configuration | include sysname');
    }

    /**
     * Get VRP version information
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('display version');
    }

    /**
     * Get the full current configuration
     *
     * @return RemoteCommandOutput
     */
    public function getCurrentConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('display current-configuration');
    }

    /**
     * Get the saved configuration
     *
     * @return RemoteCommandOutput
     */
    public function getSavedConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('display saved-configuration');
    }

    /**
     * Get a summary of all interfaces
     *
     * @return RemoteCommandOutput
     */
    public function getInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('display interface brief');
    }

    /**
     * Get detailed configuration for a specific interface
     *
     * @param string $interface Interface name (e.g. "GigabitEthernet0/0/1", "GE0/0/1")
     * @return RemoteCommandOutput
     */
    public function getInterfaceConfig(string $interface): RemoteCommandOutput
    {
        $cmd = 'display current-configuration interface ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Get interface status and counters
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function getInterfaceStatus(string $interface): RemoteCommandOutput
    {
        $cmd = 'display interface ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Get VLAN information
     *
     * @return RemoteCommandOutput
     */
    public function getVlans(): RemoteCommandOutput
    {
        return $this->remoteExec('display vlan');
    }

    /**
     * Get the IP routing table
     *
     * @return RemoteCommandOutput
     */
    public function getIpRouteTable(): RemoteCommandOutput
    {
        return $this->remoteExec('display ip routing-table');
    }

    /**
     * Get the ARP table
     *
     * @return RemoteCommandOutput
     */
    public function getArpTable(): RemoteCommandOutput
    {
        return $this->remoteExec('display arp');
    }

    /**
     * Get the MAC address table (for switches)
     *
     * @return RemoteCommandOutput
     */
    public function getMacTable(): RemoteCommandOutput
    {
        return $this->remoteExec('display mac-address');
    }

    /**
     * Get LLDP neighbors
     *
     * @return RemoteCommandOutput
     */
    public function getLldpNeighbors(): RemoteCommandOutput
    {
        return $this->remoteExec('display lldp neighbor brief');
    }

    /**
     * Get system uptime
     *
     * @return RemoteCommandOutput
     */
    public function getUptime(): RemoteCommandOutput
    {
        return $this->remoteExec('display clock');
    }

    /**
     * Get device log buffer
     *
     * @return RemoteCommandOutput
     */
    public function getLogs(): RemoteCommandOutput
    {
        return $this->remoteExec('display logbuffer');
    }

    /**
     * Get system resource usage (CPU, memory)
     *
     * @return RemoteCommandOutput
     */
    public function getResources(): RemoteCommandOutput
    {
        return $this->remoteExec('display cpu-usage && display memory-usage');
    }

    /**
     * Get device status (temperature, fan, power)
     *
     * @return RemoteCommandOutput
     */
    public function getDeviceStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('display device');
    }

    /**
     * Ping a remote host from the device
     *
     * @param string $host IP address or hostname
     * @param int $count Number of packets
     * @return RemoteCommandOutput
     */
    public function ping(string $host, int $count = 5): RemoteCommandOutput
    {
        $cmd = 'ping -c ' . $count . ' ' . self::escapeShellArgument($host);
        return $this->remoteExec($cmd);
    }

    /**
     * Traceroute from the device
     *
     * @param string $host IP address or hostname
     * @return RemoteCommandOutput
     */
    public function traceroute(string $host): RemoteCommandOutput
    {
        return $this->remoteExec('tracert ' . self::escapeShellArgument($host));
    }

    /**
     * Set the device hostname
     *
     * @param string $hostname New hostname
     * @return RemoteCommandOutput
     */
    public function setHostname(string $hostname): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && sysname ' . self::escapeShellArgument($hostname)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a MOTD banner
     *
     * @param string $message Banner message text
     * @return RemoteCommandOutput
     */
    public function setBannerMotd(string $message): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && header login information ' . self::escapeShellArgument($message)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a local user account (AAA context)
     *
     * @param string $username Username
     * @param string $password Password
     * @param string $privilege Privilege level (0-15, default 15)
     * @return RemoteCommandOutput
     */
    public function createUser(string $username, string $password, string $privilege = '15'): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && aaa'
            . ' && local-user ' . self::escapeShellArgument($username) . ' password cipher ' . self::escapeShellArgument($password)
            . ' && local-user ' . self::escapeShellArgument($username) . ' privilege level ' . $privilege
            . ' && local-user ' . self::escapeShellArgument($username) . ' service-type ssh'
            . ' && return'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a local user account
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && undo local-user ' . self::escapeShellArgument($username)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a VLAN
     *
     * @param int $vlanId VLAN ID (1-4094)
     * @param string|null $vlanName Optional VLAN name
     * @return RemoteCommandOutput
     */
    public function createVlan(int $vlanId, ?string $vlanName = null): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && vlan ' . $vlanId;

        if ($vlanName !== null) {
            $cmd .= ' && name ' . self::escapeShellArgument($vlanName);
        }

        $cmd .= ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Create VLANs in batch
     *
     * @param array<int> $vlanIds List of VLAN IDs
     * @return RemoteCommandOutput
     */
    public function createVlanBatch(array $vlanIds): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && vlan batch ' . implode(' ', $vlanIds)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a VLAN
     *
     * @param int $vlanId VLAN ID
     * @return RemoteCommandOutput
     */
    public function deleteVlan(int $vlanId): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && undo vlan ' . $vlanId
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Assign a switchport to a VLAN (access mode)
     *
     * @param string $interface Interface name
     * @param int $vlanId VLAN ID
     * @return RemoteCommandOutput
     */
    public function setInterfaceAccessVlan(string $interface, int $vlanId): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && port link-type access'
            . ' && port default vlan ' . $vlanId
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a trunk interface
     *
     * @param string $interface Interface name
     * @param string|null $allowedVlans VLANs allowed (e.g. "10 20 100-200" or null for all)
     * @param int $nativeVlan Native VLAN ID (default 1)
     * @return RemoteCommandOutput
     */
    public function setInterfaceTrunk(string $interface, ?string $allowedVlans = null, int $nativeVlan = 1): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && port link-type trunk'
            . ' && port trunk allow-pass vlan ' . ($allowedVlans !== null ? self::escapeShellArgument($allowedVlans) : 'all')
            . ' && port trunk pvid vlan ' . $nativeVlan
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a hybrid interface
     *
     * @param string $interface Interface name
     * @param string $untaggedVlans Untagged VLANs
     * @param string $taggedVlans Tagged VLANs
     * @return RemoteCommandOutput
     */
    public function setInterfaceHybrid(string $interface, string $untaggedVlans, string $taggedVlans): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && port link-type hybrid'
            . ' && port hybrid untagged vlan ' . self::escapeShellArgument($untaggedVlans)
            . ' && port hybrid tagged vlan ' . self::escapeShellArgument($taggedVlans)
            . ' && return'
            . ' && save';
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
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && description ' . self::escapeShellArgument($description)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Administratively disable an interface (shutdown)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function shutdownInterface(string $interface): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && shutdown'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Administratively enable an interface (undo shutdown)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function undoShutdownInterface(string $interface): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && undo shutdown'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure an IP address on an interface
     *
     * @param string $interface Interface name
     * @param string $ipAddress IP address
     * @param string $netmask Subnet mask
     * @return RemoteCommandOutput
     */
    public function setInterfaceIp(string $interface, string $ipAddress, string $netmask): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && ip address ' . self::escapeShellArgument($ipAddress) . ' ' . self::escapeShellArgument($netmask)
            . ' && undo shutdown'
            . ' && return'
            . ' && save';
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
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && undo ip address'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a VLANIF (SVI) interface with an IP address
     *
     * @param int $vlanId VLAN ID
     * @param string $ipAddress IP address
     * @param string $netmask Subnet mask
     * @return RemoteCommandOutput
     */
    public function createVlanif(int $vlanId, string $ipAddress, string $netmask): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface vlanif ' . $vlanId
            . ' && ip address ' . self::escapeShellArgument($ipAddress) . ' ' . self::escapeShellArgument($netmask)
            . ' && undo shutdown'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Add a static route
     *
     * @param string $destination Destination network
     * @param string $netmask Network mask
     * @param string $nextHop Next-hop IP address
     * @param int|null $preference Optional route preference (1-255)
     * @return RemoteCommandOutput
     */
    public function addStaticRoute(string $destination, string $netmask, string $nextHop, ?int $preference = null): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && ip route-static ' . self::escapeShellArgument($destination) . ' ' . self::escapeShellArgument($netmask)
            . ' ' . self::escapeShellArgument($nextHop);

        if ($preference !== null) {
            $cmd .= ' preference ' . $preference;
        }

        $cmd .= ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a static route
     *
     * @param string $destination Destination network
     * @param string $netmask Network mask
     * @param string $nextHop Next-hop IP address
     * @return RemoteCommandOutput
     */
    public function removeStaticRoute(string $destination, string $netmask, string $nextHop): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && undo ip route-static ' . self::escapeShellArgument($destination) . ' ' . self::escapeShellArgument($netmask)
            . ' ' . self::escapeShellArgument($nextHop)
            . ' && return'
            . ' && save';
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
        $cmd = 'system-view'
            . ' && ip route-static 0.0.0.0 0.0.0.0 ' . self::escapeShellArgument($gateway)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable SSH access on the device
     *
     * @param string $aaaPassword AAA password for remote login
     * @param string $serviceType Service type (ssh, telnet, all)
     * @return RemoteCommandOutput
     */
    public function enableSsh(string $aaaPassword, string $serviceType = 'ssh'): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && user-interface vty 0 4'
            . ' && authentication-mode aaa'
            . ' && protocol inbound ' . self::escapeShellArgument($serviceType)
            . ' && quit'
            . ' && aaa'
            . ' && local-user admin password cipher ' . self::escapeShellArgument($aaaPassword)
            . ' && local-user admin privilege level 15'
            . ' && local-user admin service-type ' . self::escapeShellArgument($serviceType)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Set user-interface VTY properties
     *
     * @param string $authMode Authentication mode (aaa, password, none)
     * @param int $vtyStart Start VTY line (default 0)
     * @param int $vtyEnd End VTY line (default 4)
     * @return RemoteCommandOutput
     */
    public function setVtyConfig(string $authMode = 'aaa', int $vtyStart = 0, int $vtyEnd = 4): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && user-interface vty ' . $vtyStart . ' ' . $vtyEnd
            . ' && authentication-mode ' . self::escapeShellArgument($authMode)
            . ' && protocol inbound all'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Add an ACL (basic or advanced)
     *
     * @param int $aclNumber ACL number (2000-2999 basic, 3000-3999 advanced)
     * @param string $action permit or deny
     * @param string $source Source IP with wildcard (e.g. "10.0.0.0 0.255.255.255")
     * @param string|null $destIp Optional destination IP for advanced ACL
     * @param string|null $destWildcard Optional destination wildcard for advanced ACL
     * @return RemoteCommandOutput
     */
    public function addAclRule(int $aclNumber, string $action, string $source, ?string $destIp = null, ?string $destWildcard = null): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && acl ' . $aclNumber
            . ' && rule 0 ' . $action . ' source ' . self::escapeShellArgument($source);

        if ($destIp !== null) {
            $cmd .= ' destination ' . self::escapeShellArgument($destIp);
            if ($destWildcard !== null) {
                $cmd .= ' ' . self::escapeShellArgument($destWildcard);
            }
        }

        $cmd .= ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete an ACL
     *
     * @param int $aclNumber ACL number
     * @return RemoteCommandOutput
     */
    public function deleteAcl(int $aclNumber): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && undo acl ' . $aclNumber
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Apply an ACL to an interface (inbound)
     *
     * @param string $interface Interface name
     * @param int $aclNumber ACL number
     * @return RemoteCommandOutput
     */
    public function applyAclInbound(string $interface, int $aclNumber): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && traffic-filter inbound acl ' . $aclNumber
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Apply an ACL to an interface (outbound)
     *
     * @param string $interface Interface name
     * @param int $aclNumber ACL number
     * @return RemoteCommandOutput
     */
    public function applyAclOutbound(string $interface, int $aclNumber): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && traffic-filter outbound acl ' . $aclNumber
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure NTP server
     *
     * @param string $server NTP server IP or hostname
     * @return RemoteCommandOutput
     */
    public function setNtpServer(string $server): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && ntp-service unicast-server ' . self::escapeShellArgument($server)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure SNMP community
     *
     * @param string $community Community string
     * @param string $access Access level (ro or rw)
     * @param int|null $acl Optional ACL number to restrict access
     * @return RemoteCommandOutput
     */
    public function setSnmpCommunity(string $community, string $access = 'ro', ?int $acl = null): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && snmp-agent community ' . ($access === 'rw' ? 'read-write' : 'read-only') . ' ' . self::escapeShellArgument($community);

        if ($acl !== null) {
            $cmd .= ' acl ' . $acl;
        }

        $cmd .= ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure syslog server
     *
     * @param string $server Syslog server IP
     * @param string $facility Logging facility (local0-local7)
     * @param string $severity Severity level (emergency, alert, critical, error, warning, notification, informational, debug)
     * @return RemoteCommandOutput
     */
    public function setSyslogServer(string $server, string $facility = 'local0', string $severity = 'informational'): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && info-center loghost ' . self::escapeShellArgument($server)
            . ' facility ' . self::escapeShellArgument($facility)
            . ' && info-center source default channel logbuffer log level ' . self::escapeShellArgument($severity)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable LLDP globally
     *
     * @return RemoteCommandOutput
     */
    public function enableLldp(): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && lldp enable'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable LLDP globally
     *
     * @return RemoteCommandOutput
     */
    public function disableLldp(): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && undo lldp enable'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable LLDP on an interface
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function enableInterfaceLldp(string $interface): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && lldp enable'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Create an Eth-Trunk and add member interfaces
     *
     * @param int $trunkId Eth-Trunk ID
     * @param array<string> $memberInterfaces List of member interface names
     * @param string $mode Negotiation mode (lacp, manual, static)
     * @return RemoteCommandOutput
     */
    public function createEthTrunk(int $trunkId, array $memberInterfaces, string $mode = 'lacp'): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface Eth-Trunk ' . $trunkId;

        $modeCmd = match ($mode) {
            'lacp' => 'mode lacp',
            'manual' => 'mode manual load-balance',
            'static' => 'mode manual load-balance',
            default => 'mode lacp',
        };

        $cmd .= ' && ' . $modeCmd
            . ' && quit';

        foreach ($memberInterfaces as $iface) {
            $cmd .= ' && interface ' . self::escapeShellArgument($iface)
                . ' && eth-trunk ' . $trunkId
                . ' && quit';
        }

        $cmd .= ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure interface speed and duplex
     *
     * @param string $interface Interface name
     * @param string $speed Speed (auto, 10, 100, 1000)
     * @param string $duplex Duplex (auto, half, full)
     * @return RemoteCommandOutput
     */
    public function setInterfaceSpeedDuplex(string $interface, string $speed, string $duplex): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && speed ' . self::escapeShellArgument($speed)
            . ' && duplex ' . self::escapeShellArgument($duplex)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure interface as DHCP client
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function setInterfaceDhcpClient(string $interface): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && ip address dhcp-alloc'
            . ' && undo shutdown'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Get STP (Spanning Tree Protocol) status
     *
     * @return RemoteCommandOutput
     */
    public function getStpStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('display stp brief');
    }

    /**
     * Enable STP globally
     *
     * @return RemoteCommandOutput
     */
    public function enableStp(): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && stp enable'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable STP globally
     *
     * @return RemoteCommandOutput
     */
    public function disableStp(): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && undo stp enable'
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Set STP mode
     *
     * @param string $mode STP mode (stp, rstp, mstp)
     * @return RemoteCommandOutput
     */
    public function setStpMode(string $mode): RemoteCommandOutput
    {
        $cmd = 'system-view'
            . ' && stp mode ' . self::escapeShellArgument($mode)
            . ' && return'
            . ' && save';
        return $this->remoteExec($cmd);
    }

    /**
     * Get the port VLAN information for a specific interface
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function getInterfaceVlanInfo(string $interface): RemoteCommandOutput
    {
        $cmd = 'display port vlan active ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Get link aggregation information
     *
     * @return RemoteCommandOutput
     */
    public function getLinkAggregation(): RemoteCommandOutput
    {
        return $this->remoteExec('display eth-trunk');
    }

    /**
     * Get OSPF neighbor information
     *
     * @return RemoteCommandOutput
     */
    public function getOspfNeighbors(): RemoteCommandOutput
    {
        return $this->remoteExec('display ospf peer brief');
    }

    /**
     * Execute an arbitrary VRP command (user view)
     *
     * @param string $command Any VRP display or exec command (user view)
     * @return RemoteCommandOutput
     */
    public function exec(string $command): RemoteCommandOutput
    {
        return $this->remoteExec($command);
    }
}
