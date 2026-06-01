<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Cisco IOS/IOS-XE network equipment operations
 *
 * Provides methods for configuring Cisco switches and routers
 * via SSH using IOS CLI syntax. All arguments are escaped.
 *
 * Most config methods follow: enable → configure terminal → ... → end → write memory
 */
class CiscoOps extends AbstractOps
{
    /**
     * Enter enable mode (privileged exec)
     *
     * @param string|null $enableSecret Optional enable secret password
     * @return RemoteCommandOutput
     */
    public function enterEnableMode(?string $enableSecret = null): RemoteCommandOutput
    {
        if ($enableSecret !== null) {
            return $this->remoteExec('enable ' . self::escapeShellArgument($enableSecret));
        }
        return $this->remoteExec('enable');
    }

    /**
     * Save the running configuration to startup configuration
     *
     * @return RemoteCommandOutput
     */
    public function saveConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('write memory');
    }

    /**
     * Disable pagination for the current session
     *
     * @return RemoteCommandOutput
     */
    public function disablePagination(): RemoteCommandOutput
    {
        return $this->remoteExec('terminal length 0');
    }

    /**
     * Get the device hostname
     *
     * @return RemoteCommandOutput
     */
    public function getHostname(): RemoteCommandOutput
    {
        return $this->remoteExec('show running-config | include hostname');
    }

    /**
     * Get IOS version information
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('show version');
    }

    /**
     * Get the full running configuration
     *
     * @return RemoteCommandOutput
     */
    public function getRunningConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('show running-config');
    }

    /**
     * Get the startup configuration
     *
     * @return RemoteCommandOutput
     */
    public function getStartupConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('show startup-config');
    }

    /**
     * Get a summary of all interfaces
     *
     * @return RemoteCommandOutput
     */
    public function getInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('show ip interface brief');
    }

    /**
     * Get detailed configuration for a specific interface
     *
     * @param string $interface Interface name (e.g. "GigabitEthernet0/1")
     * @return RemoteCommandOutput
     */
    public function getInterfaceConfig(string $interface): RemoteCommandOutput
    {
        $cmd = 'show running-config interface ' . self::escapeShellArgument($interface);
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
        $cmd = 'show interfaces ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Get VLAN information
     *
     * @return RemoteCommandOutput
     */
    public function getVlans(): RemoteCommandOutput
    {
        return $this->remoteExec('show vlan brief');
    }

    /**
     * Get the IP routing table
     *
     * @return RemoteCommandOutput
     */
    public function getIpRoute(): RemoteCommandOutput
    {
        return $this->remoteExec('show ip route');
    }

    /**
     * Get ARP table
     *
     * @return RemoteCommandOutput
     */
    public function getArpTable(): RemoteCommandOutput
    {
        return $this->remoteExec('show ip arp');
    }

    /**
     * Get MAC address table (for switches)
     *
     * @return RemoteCommandOutput
     */
    public function getMacTable(): RemoteCommandOutput
    {
        return $this->remoteExec('show mac address-table');
    }

    /**
     * Get CDP neighbors
     *
     * @return RemoteCommandOutput
     */
    public function getCdpNeighbors(): RemoteCommandOutput
    {
        return $this->remoteExec('show cdp neighbors detail');
    }

    /**
     * Get LLDP neighbors
     *
     * @return RemoteCommandOutput
     */
    public function getLldpNeighbors(): RemoteCommandOutput
    {
        return $this->remoteExec('show lldp neighbors detail');
    }

    /**
     * Get system uptime
     *
     * @return RemoteCommandOutput
     */
    public function getUptime(): RemoteCommandOutput
    {
        return $this->remoteExec('show uptime');
    }

    /**
     * Get logging information
     *
     * @return RemoteCommandOutput
     */
    public function getLogs(): RemoteCommandOutput
    {
        return $this->remoteExec('show log');
    }

    /**
     * Get environment status (temperature, voltage, fans)
     *
     * @return RemoteCommandOutput
     */
    public function getEnvironment(): RemoteCommandOutput
    {
        return $this->remoteExec('show environment');
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
        $cmd = 'ping ' . self::escapeShellArgument($host);
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
        return $this->remoteExec('traceroute ' . self::escapeShellArgument($host));
    }

    /**
     * Set the device hostname
     *
     * @param string $hostname New hostname
     * @return RemoteCommandOutput
     */
    public function setHostname(string $hostname): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && hostname ' . self::escapeShellArgument($hostname)
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Set a MOTD banner
     *
     * @param string $message Banner message text
     * @return RemoteCommandOutput
     */
    public function setBannerMotd(string $message): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && banner motd ^' . self::escapeShellArgument($message) . '^'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a local user account
     *
     * @param string $username Username
     * @param string $password Password
     * @param int $privilege Privilege level (0-15, default 15)
     * @return RemoteCommandOutput
     */
    public function createUser(string $username, string $password, int $privilege = 15): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && username ' . self::escapeShellArgument($username)
            . ' privilege ' . $privilege
            . ' secret ' . self::escapeShellArgument($password)
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && no username ' . self::escapeShellArgument($username)
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable SSH access on the device
     *
     * @param string $domainName Domain name (for key generation)
     * @param int $keySize RSA key size (default 2048)
     * @param int $sshVersion SSH version (default 2)
     * @return RemoteCommandOutput
     */
    public function enableSsh(string $domainName, int $keySize = 2048, int $sshVersion = 2): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && ip domain-name ' . self::escapeShellArgument($domainName)
            . ' && crypto key generate rsa modulus ' . $keySize
            . ' && ip ssh version ' . $sshVersion
            . ' && line vty 0 4'
            . ' && transport input ssh'
            . ' && login local'
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a VLAN
     *
     * @param int $vlanId VLAN ID (1-4094)
     * @param string $vlanName VLAN name
     * @return RemoteCommandOutput
     */
    public function createVlan(int $vlanId, string $vlanName): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && vlan ' . $vlanId
            . ' && name ' . self::escapeShellArgument($vlanName)
            . ' && exit'
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && no vlan ' . $vlanId
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && switchport mode access'
            . ' && switchport access vlan ' . $vlanId
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a trunk interface
     *
     * @param string $interface Interface name
     * @param string|null $allowedVlans VLANs allowed (e.g. "1,10,100-200" or null for all)
     * @param string $nativeVlan Native VLAN ID
     * @return RemoteCommandOutput
     */
    public function setInterfaceTrunk(string $interface, ?string $allowedVlans = null, string $nativeVlan = '1'): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && switchport mode trunk'
            . ' && switchport trunk native vlan ' . $nativeVlan;

        if ($allowedVlans !== null) {
            $cmd .= ' && switchport trunk allowed vlan ' . self::escapeShellArgument($allowedVlans);
        }

        $cmd .= ' && exit'
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && description ' . self::escapeShellArgument($description)
            . ' && exit'
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && speed ' . self::escapeShellArgument($speed)
            . ' && duplex ' . self::escapeShellArgument($duplex)
            . ' && exit'
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && shutdown'
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Administratively enable an interface (no shutdown)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function noShutdownInterface(string $interface): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && no shutdown'
            . ' && exit'
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && ip address ' . self::escapeShellArgument($ipAddress) . ' ' . self::escapeShellArgument($netmask)
            . ' && no shutdown'
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a VLAN interface (SVI) with an IP address
     *
     * @param int $vlanId VLAN ID
     * @param string $ipAddress IP address
     * @param string $netmask Subnet mask
     * @return RemoteCommandOutput
     */
    public function createVlanInterface(int $vlanId, string $ipAddress, string $netmask): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface vlan ' . $vlanId
            . ' && ip address ' . self::escapeShellArgument($ipAddress) . ' ' . self::escapeShellArgument($netmask)
            . ' && no shutdown'
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Add a static route
     *
     * @param string $network Destination network
     * @param string $netmask Network mask
     * @param string $nextHop Next-hop IP address
     * @param int|null $distance Optional administrative distance
     * @return RemoteCommandOutput
     */
    public function addStaticRoute(string $network, string $netmask, string $nextHop, ?int $distance = null): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && ip route ' . self::escapeShellArgument($network) . ' ' . self::escapeShellArgument($netmask)
            . ' ' . self::escapeShellArgument($nextHop);

        if ($distance !== null) {
            $cmd .= ' ' . $distance;
        }

        $cmd .= ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a static route
     *
     * @param string $network Destination network
     * @param string $netmask Network mask
     * @param string $nextHop Next-hop IP address
     * @return RemoteCommandOutput
     */
    public function removeStaticRoute(string $network, string $netmask, string $nextHop): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && no ip route ' . self::escapeShellArgument($network) . ' ' . self::escapeShellArgument($netmask)
            . ' ' . self::escapeShellArgument($nextHop)
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && ip default-gateway ' . self::escapeShellArgument($gateway)
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Add an IP access-list (numbered, standard)
     *
     * @param int $aclNumber ACL number (1-99 standard, 100-199 extended)
     * @param string $action permit or deny
     * @param string $source Source IP with wildcard (e.g. "10.0.0.0 0.0.0.255")
     * @return RemoteCommandOutput
     */
    public function addAclRule(int $aclNumber, string $action, string $source): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && access-list ' . $aclNumber . ' ' . $action . ' ' . self::escapeShellArgument($source)
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove an IP access-list
     *
     * @param int $aclNumber ACL number
     * @return RemoteCommandOutput
     */
    public function removeAcl(int $aclNumber): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && no access-list ' . $aclNumber
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a named extended ACL entry
     *
     * @param string $aclName ACL name
     * @param string $action permit or deny
     * @param string $protocol Protocol (ip, tcp, udp, icmp)
     * @param string $source Source IP with wildcard (e.g. "any" or "10.0.0.0 0.0.0.255")
     * @param string $destination Destination IP with wildcard
     * @param int|null $dstPort Optional destination port (for tcp/udp)
     * @return RemoteCommandOutput
     */
    public function addNamedAclEntry(
        string $aclName,
        string $action,
        string $protocol,
        string $source,
        string $destination,
        ?int $dstPort = null
    ): RemoteCommandOutput {
        $cmd = 'configure terminal'
            . ' && ip access-list extended ' . self::escapeShellArgument($aclName)
            . ' && ' . $action . ' ' . $protocol . ' ' . self::escapeShellArgument($source)
            . ' ' . self::escapeShellArgument($destination);

        if ($dstPort !== null) {
            $cmd .= ' eq ' . $dstPort;
        }

        $cmd .= ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Apply an ACL to an interface (inbound)
     *
     * @param string $interface Interface name
     * @param string $aclName ACL name or number
     * @return RemoteCommandOutput
     */
    public function applyAclInbound(string $interface, string $aclName): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && ip access-group ' . self::escapeShellArgument($aclName) . ' in'
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Apply an ACL to an interface (outbound)
     *
     * @param string $interface Interface name
     * @param string $aclName ACL name or number
     * @return RemoteCommandOutput
     */
    public function applyAclOutbound(string $interface, string $aclName): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && ip access-group ' . self::escapeShellArgument($aclName) . ' out'
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable NetFlow on the device
     *
     * @param string $collectorIp NetFlow collector IP
     * @param int $collectorPort Collector UDP port (default 2055)
     * @param string $sourceInterface Source interface for flow records
     * @return RemoteCommandOutput
     */
    public function enableNetflow(string $collectorIp, int $collectorPort = 2055, string $sourceInterface = 'Loopback0'): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && ip flow-export destination ' . self::escapeShellArgument($collectorIp) . ' ' . $collectorPort
            . ' && ip flow-export source ' . self::escapeShellArgument($sourceInterface)
            . ' && ip flow-export version 9'
            . ' && ip flow-cache timeout active 1'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable NetFlow on a specific interface (ingress)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function enableInterfaceNetflow(string $interface): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && ip flow ingress'
            . ' && ip flow egress'
            . ' && exit'
            . ' && end'
            . ' && write memory';
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
        $cmd = 'configure terminal'
            . ' && ntp server ' . self::escapeShellArgument($server)
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure SNMP community string
     *
     * @param string $community Community string
     * @param string $access Access level (ro or rw)
     * @param string|null $acl Optional ACL to restrict SNMP access
     * @return RemoteCommandOutput
     */
    public function setSnmpCommunity(string $community, string $access = 'ro', ?string $acl = null): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && snmp-server community ' . self::escapeShellArgument($community) . ' ' . $access;

        if ($acl !== null) {
            $cmd .= ' ' . self::escapeShellArgument($acl);
        }

        $cmd .= ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure syslog server
     *
     * @param string $server Syslog server IP
     * @param string $level Logging level (emergencies, alerts, critical, errors, warnings, notifications, informational, debugging)
     * @return RemoteCommandOutput
     */
    public function setSyslogServer(string $server, string $level = 'informational'): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && logging host ' . self::escapeShellArgument($server)
            . ' && logging trap ' . self::escapeShellArgument($level)
            . ' && logging on'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable CDP globally
     *
     * @return RemoteCommandOutput
     */
    public function enableCdp(): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && cdp run'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable CDP globally
     *
     * @return RemoteCommandOutput
     */
    public function disableCdp(): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && no cdp run'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable LLDP globally
     *
     * @return RemoteCommandOutput
     */
    public function enableLldp(): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && lldp run'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable LLDP globally
     *
     * @return RemoteCommandOutput
     */
    public function disableLldp(): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && no lldp run'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure an interface as DHCP client
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function setInterfaceDhcpClient(string $interface): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface ' . self::escapeShellArgument($interface)
            . ' && ip address dhcp'
            . ' && no shutdown'
            . ' && exit'
            . ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a port-channel and add member interfaces
     *
     * @param int $channelNumber Port-channel number
     * @param array<string> $memberInterfaces List of member interface names
     * @param string $mode Mode (active, passive, on)
     * @return RemoteCommandOutput
     */
    public function createPortChannel(int $channelNumber, array $memberInterfaces, string $mode = 'active'): RemoteCommandOutput
    {
        $cmd = 'configure terminal'
            . ' && interface port-channel ' . $channelNumber
            . ' && exit';

        foreach ($memberInterfaces as $iface) {
            $cmd .= ' && interface ' . self::escapeShellArgument($iface)
                . ' && channel-group ' . $channelNumber . ' mode ' . self::escapeShellArgument($mode)
                . ' && exit';
        }

        $cmd .= ' && end'
            . ' && write memory';
        return $this->remoteExec($cmd);
    }

    /**
     * Execute arbitrary IOS commands in privileged exec mode
     *
     * @param string $command Any IOS show or exec command
     * @return RemoteCommandOutput
     */
    public function exec(string $command): RemoteCommandOutput
    {
        return $this->remoteExec($command);
    }
}
