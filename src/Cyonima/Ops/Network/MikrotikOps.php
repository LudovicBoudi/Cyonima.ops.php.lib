<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * MikroTik RouterOS network equipment operations
 *
 * Provides methods for configuring MikroTik routers, switches, and wireless
 * devices via SSH using the RouterOS CLI syntax.
 *
 * RouterOS uses a hierarchical command structure with '/' prefixes.
 * Configuration changes take effect immediately (no commit step).
 * Values with spaces must be quoted with double quotes.
 *
 * @see https://wiki.mikrotik.com/wiki/Manual:CLI
 */
class MikrotikOps extends AbstractOps
{
    /**
     * Get the system identity (hostname)
     *
     * @return RemoteCommandOutput
     */
    public function getSystemIdentity(): RemoteCommandOutput
    {
        return $this->remoteExec('/system identity print');
    }

    /**
     * Set the system identity (hostname)
     *
     * @param string $name New identity name
     * @return RemoteCommandOutput
     */
    public function setSystemIdentity(string $name): RemoteCommandOutput
    {
        $cmd = '/system identity set name=' . self::escapeMikrotikArgument($name);
        return $this->remoteExec($cmd);
    }

    /**
     * Get RouterOS version and system resource info
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('/system resource print');
    }

    /**
     * Get system uptime
     *
     * @return RemoteCommandOutput
     */
    public function getUptime(): RemoteCommandOutput
    {
        return $this->remoteExec('/system resource uptime print');
    }

    /**
     * Get detailed system resource usage (CPU, memory, disk, uptime)
     *
     * @return RemoteCommandOutput
     */
    public function getResources(): RemoteCommandOutput
    {
        return $this->remoteExec('/system resource print');
    }

    /**
     * Get RouterBoard health (temperature, voltage, etc.)
     *
     * @return RemoteCommandOutput
     */
    public function getHealth(): RemoteCommandOutput
    {
        return $this->remoteExec('/system health print');
    }

    /**
     * Get system clock and date
     *
     * @return RemoteCommandOutput
     */
    public function getClock(): RemoteCommandOutput
    {
        return $this->remoteExec('/system clock print');
    }

    /**
     * Set system clock
     *
     * @param string $dateTime Date/time string RouterOS format (e.g. "2024-01-15 14:30:00")
     * @return RemoteCommandOutput
     */
    public function setClock(string $dateTime): RemoteCommandOutput
    {
        $cmd = '/system clock set date=' . self::escapeMikrotikArgument($dateTime)
            . ' time=' . self::escapeMikrotikArgument($dateTime);
        return $this->remoteExec($cmd);
    }

    /**
     * Reboot the device
     *
     * @return RemoteCommandOutput
     */
    public function reboot(): RemoteCommandOutput
    {
        return $this->remoteExec('/system reboot');
    }

    /**
     * Shutdown the device
     *
     * @return RemoteCommandOutput
     */
    public function shutdown(): RemoteCommandOutput
    {
        return $this->remoteExec('/system shutdown');
    }

    /**
     * Get all interfaces with their statuses
     *
     * @return RemoteCommandOutput
     */
    public function getInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('/interface print');
    }

    /**
     * Get detailed information for a specific interface
     *
     * @param string $interface Interface name (e.g. "ether1", "wlan1")
     * @return RemoteCommandOutput
     */
    public function getInterfaceDetail(string $interface): RemoteCommandOutput
    {
        $cmd = '/interface print detail where name=' . self::escapeMikrotikArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Enable an interface
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function enableInterface(string $interface): RemoteCommandOutput
    {
        $cmd = '/interface enable ' . self::escapeMikrotikArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Disable an interface
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function disableInterface(string $interface): RemoteCommandOutput
    {
        $cmd = '/interface disable ' . self::escapeMikrotikArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Set a comment/description on an interface
     *
     * @param string $interface Interface name
     * @param string $comment Comment text
     * @return RemoteCommandOutput
     */
    public function setInterfaceComment(string $interface, string $comment): RemoteCommandOutput
    {
        $cmd = '/interface set [find name=' . self::escapeMikrotikArgument($interface) . '] comment=' . self::escapeMikrotikArgument($comment);
        return $this->remoteExec($cmd);
    }

    /**
     * Set an interface's master-port (bridge-like binding)
     *
     * @param string $interface Interface name
     * @param string|null $masterPort Master interface name, or null to clear
     * @return RemoteCommandOutput
     */
    public function setInterfaceMasterPort(string $interface, ?string $masterPort = null): RemoteCommandOutput
    {
        $cmd = '/interface set [find name=' . self::escapeMikrotikArgument($interface) . ']';
        if ($masterPort !== null) {
            $cmd .= ' master-port=' . self::escapeMikrotikArgument($masterPort);
        } else {
            $cmd .= ' master-port=none';
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Get IP addresses configured on the device
     *
     * @return RemoteCommandOutput
     */
    public function getIpAddresses(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip address print');
    }

    /**
     * Add an IP address to an interface
     *
     * @param string $address IP address with prefix (e.g. "10.0.0.1/24")
     * @param string $interface Interface name
     * @param string|null $network Optional network address
     * @return RemoteCommandOutput
     */
    public function addIpAddress(string $address, string $interface, ?string $network = null): RemoteCommandOutput
    {
        $cmd = '/ip address add address=' . self::escapeMikrotikArgument($address)
            . ' interface=' . self::escapeMikrotikArgument($interface);
        if ($network !== null) {
            $cmd .= ' network=' . self::escapeMikrotikArgument($network);
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Remove an IP address
     *
     * @param string $address IP address with prefix
     * @param string $interface Interface name to disambiguate
     * @return RemoteCommandOutput
     */
    public function removeIpAddress(string $address, string $interface): RemoteCommandOutput
    {
        $cmd = '/ip address remove [find address=' . self::escapeMikrotikArgument($address)
            . ' and interface=' . self::escapeMikrotikArgument($interface) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Get the routing table
     *
     * @return RemoteCommandOutput
     */
    public function getRoutes(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip route print');
    }

    /**
     * Add a static route
     *
     * @param string $destination Destination prefix (e.g. "10.0.0.0/8")
     * @param string $gateway Gateway IP address
     * @param int|null $distance Optional distance value
     * @return RemoteCommandOutput
     */
    public function addRoute(string $destination, string $gateway, ?int $distance = null): RemoteCommandOutput
    {
        $cmd = '/ip route add dst-address=' . self::escapeMikrotikArgument($destination)
            . ' gateway=' . self::escapeMikrotikArgument($gateway);
        if ($distance !== null) {
            $cmd .= ' distance=' . $distance;
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a static route
     *
     * @param string $destination Destination prefix
     * @return RemoteCommandOutput
     */
    public function removeRoute(string $destination): RemoteCommandOutput
    {
        $cmd = '/ip route remove [find dst-address=' . self::escapeMikrotikArgument($destination) . ']';
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
        $cmd = '/ip route add dst-address=0.0.0.0/0 gateway=' . self::escapeMikrotikArgument($gateway);
        return $this->remoteExec($cmd);
    }

    /**
     * Get the ARP table
     *
     * @return RemoteCommandOutput
     */
    public function getArpTable(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip arp print');
    }

    /**
     * Get DHCP server leases
     *
     * @return RemoteCommandOutput
     */
    public function getDhcpLeases(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip dhcp-server lease print');
    }

    /**
     * Get DHCP server list
     *
     * @return RemoteCommandOutput
     */
    public function getDhcpServers(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip dhcp-server print');
    }

    /**
     * Get DNS cache entries
     *
     * @return RemoteCommandOutput
     */
    public function getDnsCache(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip dns cache print');
    }

    /**
     * Set DNS servers
     *
     * @param array<string> $servers List of DNS server IPs
     * @return RemoteCommandOutput
     */
    public function setDnsServers(array $servers): RemoteCommandOutput
    {
        $escapedServers = [];
        foreach ($servers as $server) {
            $escapedServers[] = self::escapeMikrotikArgument($server);
        }
        $cmd = '/ip dns set servers=' . implode(',', $escapedServers) . ' allow-remote-requests=yes';
        return $this->remoteExec($cmd);
    }

    /**
     * Get firewall filter rules
     *
     * @return RemoteCommandOutput
     */
    public function getFirewallRules(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip firewall filter print');
    }

    /**
     * Add a firewall filter rule
     *
     * @param string $chain Chain name (input, output, forward)
     * @param string $action Action (accept, drop, reject, fasttrack-connection)
     * @param string|null $protocol Protocol (tcp, udp, icmp) or null for any
     * @param string|null $srcAddress Source IP with prefix or null for any
     * @param string|null $dstAddress Destination IP with prefix or null for any
     * @param int|null $dstPort Destination port or null for any
     * @param string|null $inInterface Input interface or null for any
     * @param string|null $outInterface Output interface or null for any
     * @param string|null $comment Optional comment
     * @return RemoteCommandOutput
     */
    public function addFirewallRule(
        string $chain,
        string $action,
        ?string $protocol = null,
        ?string $srcAddress = null,
        ?string $dstAddress = null,
        ?int $dstPort = null,
        ?string $inInterface = null,
        ?string $outInterface = null,
        ?string $comment = null
    ): RemoteCommandOutput {
        $cmd = '/ip firewall filter add chain=' . self::escapeMikrotikArgument($chain)
            . ' action=' . self::escapeMikrotikArgument($action);

        if ($protocol !== null) {
            $cmd .= ' protocol=' . self::escapeMikrotikArgument($protocol);
        }
        if ($srcAddress !== null) {
            $cmd .= ' src-address=' . self::escapeMikrotikArgument($srcAddress);
        }
        if ($dstAddress !== null) {
            $cmd .= ' dst-address=' . self::escapeMikrotikArgument($dstAddress);
        }
        if ($dstPort !== null) {
            $cmd .= ' dst-port=' . $dstPort;
        }
        if ($inInterface !== null) {
            $cmd .= ' in-interface=' . self::escapeMikrotikArgument($inInterface);
        }
        if ($outInterface !== null) {
            $cmd .= ' out-interface=' . self::escapeMikrotikArgument($outInterface);
        }
        if ($comment !== null) {
            $cmd .= ' comment=' . self::escapeMikrotikArgument($comment);
        }

        return $this->remoteExec($cmd);
    }

    /**
     * Remove a firewall rule by number
     *
     * @param int $ruleNumber Rule number from the print list
     * @return RemoteCommandOutput
     */
    public function removeFirewallRule(int $ruleNumber): RemoteCommandOutput
    {
        $cmd = '/ip firewall filter remove numbers=' . $ruleNumber;
        return $this->remoteExec($cmd);
    }

    /**
     * Enable a firewall rule by number
     *
     * @param int $ruleNumber Rule number
     * @return RemoteCommandOutput
     */
    public function enableFirewallRule(int $ruleNumber): RemoteCommandOutput
    {
        $cmd = '/ip firewall filter enable numbers=' . $ruleNumber;
        return $this->remoteExec($cmd);
    }

    /**
     * Disable a firewall rule by number
     *
     * @param int $ruleNumber Rule number
     * @return RemoteCommandOutput
     */
    public function disableFirewallRule(int $ruleNumber): RemoteCommandOutput
    {
        $cmd = '/ip firewall filter disable numbers=' . $ruleNumber;
        return $this->remoteExec($cmd);
    }

    /**
     * Add a NAT rule (srcnat or dstnat)
     *
     * @param string $chain Chain (srcnat, dstnat)
     * @param string $action Action (masquerade, src-nat, dst-nat, redirect)
     * @param string|null $srcAddress Source IP or null for any
     * @param string|null $dstAddress Destination IP or null for any
     * @param int|null $toPorts Optional destination port for redirect
     * @return RemoteCommandOutput
     */
    public function addNatRule(
        string $chain,
        string $action,
        ?string $srcAddress = null,
        ?string $dstAddress = null,
        ?int $toPorts = null
    ): RemoteCommandOutput {
        $cmd = '/ip firewall nat add chain=' . self::escapeMikrotikArgument($chain)
            . ' action=' . self::escapeMikrotikArgument($action);

        if ($srcAddress !== null) {
            $cmd .= ' src-address=' . self::escapeMikrotikArgument($srcAddress);
        }
        if ($dstAddress !== null) {
            $cmd .= ' dst-address=' . self::escapeMikrotikArgument($dstAddress);
        }
        if ($toPorts !== null) {
            $cmd .= ' to-ports=' . $toPorts;
        }

        return $this->remoteExec($cmd);
    }

    /**
     * Get NAT rules
     *
     * @return RemoteCommandOutput
     */
    public function getNatRules(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip firewall nat print');
    }

    /**
     * Get configured VLANs
     *
     * @return RemoteCommandOutput
     */
    public function getVlans(): RemoteCommandOutput
    {
        return $this->remoteExec('/interface vlan print');
    }

    /**
     * Create a VLAN interface
     *
     * @param int $vlanId VLAN ID (1-4094)
     * @param string $name Interface name (e.g. "vlan100")
     * @param string $interface Physical interface name
     * @return RemoteCommandOutput
     */
    public function createVlan(int $vlanId, string $name, string $interface): RemoteCommandOutput
    {
        $cmd = '/interface vlan add vlan-id=' . $vlanId
            . ' name=' . self::escapeMikrotikArgument($name)
            . ' interface=' . self::escapeMikrotikArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a VLAN interface
     *
     * @param string $name VLAN interface name
     * @return RemoteCommandOutput
     */
    public function removeVlan(string $name): RemoteCommandOutput
    {
        $cmd = '/interface vlan remove [find name=' . self::escapeMikrotikArgument($name) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Get bridge list
     *
     * @return RemoteCommandOutput
     */
    public function getBridges(): RemoteCommandOutput
    {
        return $this->remoteExec('/interface bridge print');
    }

    /**
     * Create a bridge interface
     *
     * @param string $name Bridge name (e.g. "bridge1")
     * @param string|null $comment Optional comment
     * @return RemoteCommandOutput
     */
    public function createBridge(string $name, ?string $comment = null): RemoteCommandOutput
    {
        $cmd = '/interface bridge add name=' . self::escapeMikrotikArgument($name);
        if ($comment !== null) {
            $cmd .= ' comment=' . self::escapeMikrotikArgument($comment);
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a bridge interface
     *
     * @param string $name Bridge name
     * @return RemoteCommandOutput
     */
    public function removeBridge(string $name): RemoteCommandOutput
    {
        $cmd = '/interface bridge remove [find name=' . self::escapeMikrotikArgument($name) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Add a port to a bridge
     *
     * @param string $bridge Bridge name
     * @param string $interface Interface name to add
     * @return RemoteCommandOutput
     */
    public function addBridgePort(string $bridge, string $interface): RemoteCommandOutput
    {
        $cmd = '/interface bridge port add bridge=' . self::escapeMikrotikArgument($bridge)
            . ' interface=' . self::escapeMikrotikArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a port from a bridge
     *
     * @param string $bridge Bridge name
     * @param string $interface Interface name to remove
     * @return RemoteCommandOutput
     */
    public function removeBridgePort(string $bridge, string $interface): RemoteCommandOutput
    {
        $cmd = '/interface bridge port remove [find bridge=' . self::escapeMikrotikArgument($bridge)
            . ' and interface=' . self::escapeMikrotikArgument($interface) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Get bridge port list
     *
     * @return RemoteCommandOutput
     */
    public function getBridgePorts(): RemoteCommandOutput
    {
        return $this->remoteExec('/interface bridge port print');
    }

    /**
     * Get wireless registration table (connected wireless clients)
     *
     * @return RemoteCommandOutput
     */
    public function getWirelessRegistrations(): RemoteCommandOutput
    {
        return $this->remoteExec('/interface wireless registration-table print');
    }

    /**
     * Get CAPsMAN registration table (managed wireless clients)
     *
     * @return RemoteCommandOutput
     */
    public function getCapsmanRegistrations(): RemoteCommandOutput
    {
        return $this->remoteExec('/caps-man registration-table print');
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
        $cmd = '/ping ' . self::escapeMikrotikArgument($host)
            . ' count=' . $count
            . ' size=' . $size;
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
        $cmd = '/tool traceroute ' . self::escapeMikrotikArgument($host);
        return $this->remoteExec($cmd);
    }

    /**
     * Get system log entries
     *
     * @param int $lines Number of lines (default 100)
     * @return RemoteCommandOutput
     */
    public function getLog(int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('/log print without-paging where topics~"system"');
    }

    /**
     * Get log entries for specific topics
     *
     * @param string $topics Topic filter (e.g. "interface", "dhcp", "wireless")
     * @param int $lines Number of lines
     * @return RemoteCommandOutput
     */
    public function getLogByTopic(string $topics, int $lines = 100): RemoteCommandOutput
    {
        $cmd = '/log print without-paging where topics~' . self::escapeMikrotikArgument($topics);
        return $this->remoteExec($cmd);
    }

    /**
     * Get configured users
     *
     * @return RemoteCommandOutput
     */
    public function getUsers(): RemoteCommandOutput
    {
        return $this->remoteExec('/user print');
    }

    /**
     * Create a local user account
     *
     * @param string $username Username
     * @param string $password Password
     * @param string $group User group (full, read, write, etc.)
     * @return RemoteCommandOutput
     */
    public function createUser(string $username, string $password, string $group = 'full'): RemoteCommandOutput
    {
        $cmd = '/user add name=' . self::escapeMikrotikArgument($username)
            . ' password=' . self::escapeMikrotikArgument($password)
            . ' group=' . self::escapeMikrotikArgument($group);
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a user account
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function removeUser(string $username): RemoteCommandOutput
    {
        $cmd = '/user remove [find name=' . self::escapeMikrotikArgument($username) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Set user password
     *
     * @param string $username Username
     * @param string $password New password
     * @return RemoteCommandOutput
     */
    public function setUserPassword(string $username, string $password): RemoteCommandOutput
    {
        $cmd = '/user set [find name=' . self::escapeMikrotikArgument($username) . '] password=' . self::escapeMikrotikArgument($password);
        return $this->remoteExec($cmd);
    }

    /**
     * Get user group list
     *
     * @return RemoteCommandOutput
     */
    public function getUserGroups(): RemoteCommandOutput
    {
        return $this->remoteExec('/user group print');
    }

    /**
     * Get SNMP communities
     *
     * @return RemoteCommandOutput
     */
    public function getSnmpCommunities(): RemoteCommandOutput
    {
        return $this->remoteExec('/snmp community print');
    }

    /**
     * Add an SNMP community
     *
     * @param string $name Community name
     * @param string $access Access (read-only, read-write)
     * @param string|null $subnet Allowed subnet (e.g. "10.0.0.0/8")
     * @return RemoteCommandOutput
     */
    public function addSnmpCommunity(string $name, string $access = 'read-only', ?string $subnet = null): RemoteCommandOutput
    {
        $cmd = '/snmp community add name=' . self::escapeMikrotikArgument($name)
            . ' address=' . self::escapeMikrotikArgument($subnet ?? '0.0.0.0/0')
            . ' security=' . self::escapeMikrotikArgument($access);
        return $this->remoteExec($cmd);
    }

    /**
     * Remove an SNMP community
     *
     * @param string $name Community name
     * @return RemoteCommandOutput
     */
    public function removeSnmpCommunity(string $name): RemoteCommandOutput
    {
        $cmd = '/snmp community remove [find name=' . self::escapeMikrotikArgument($name) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable SNMP service
     *
     * @return RemoteCommandOutput
     */
    public function enableSnmp(): RemoteCommandOutput
    {
        return $this->remoteExec('/snmp set enabled=yes');
    }

    /**
     * Disable SNMP service
     *
     * @return RemoteCommandOutput
     */
    public function disableSnmp(): RemoteCommandOutput
    {
        return $this->remoteExec('/snmp set enabled=no');
    }

    /**
     * Configure NTP client
     *
     * @param string $server NTP server hostname or IP
     * @return RemoteCommandOutput
     */
    public function setNtpServer(string $server): RemoteCommandOutput
    {
        $cmd = '/system ntp client set enabled=yes server-dns-names=' . self::escapeMikrotikArgument($server);
        return $this->remoteExec($cmd);
    }

    /**
     * Disable NTP client
     *
     * @return RemoteCommandOutput
     */
    public function disableNtpClient(): RemoteCommandOutput
    {
        return $this->remoteExec('/system ntp client set enabled=no');
    }

    /**
     * Configure syslog remote logging
     *
     * @param string $server Remote syslog server IP
     * @param int $port Remote syslog port (default 514)
     * @return RemoteCommandOutput
     */
    public function setSyslogServer(string $server, int $port = 514): RemoteCommandOutput
    {
        $cmd = '/system logging action set 0 remote=' . self::escapeMikrotikArgument($server)
            . ' remote-port=' . $port;
        return $this->remoteExec($cmd);
    }

    /**
     * Add a remote logging rule
     *
     * @param string $server Remote syslog server IP
     * @param string $action Action name (e.g. "remote")
     * @param string $topics Topics to log (e.g. "info", "error", "warning")
     * @return RemoteCommandOutput
     */
    public function addLogRule(string $server, string $action = 'remote', string $topics = 'info'): RemoteCommandOutput
    {
        $cmd = '/system logging add action=' . self::escapeMikrotikArgument($action)
            . ' topics=' . self::escapeMikrotikArgument($topics)
            . ' remote=' . self::escapeMikrotikArgument($server);
        return $this->remoteExec($cmd);
    }

    /**
     * Export the full configuration
     *
     * @return RemoteCommandOutput
     */
    public function exportConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('/export');
    }

    /**
     * Export the configuration in terse format (one-liners)
     *
     * @return RemoteCommandOutput
     */
    public function exportConfigTerse(): RemoteCommandOutput
    {
        return $this->remoteExec('/export terse');
    }

    /**
     * Export configuration without verbose defaults
     *
     * @return RemoteCommandOutput
     */
    public function exportConfigCompact(): RemoteCommandOutput
    {
        return $this->remoteExec('/export compact');
    }

    /**
     * Save a system backup file
     *
     * @param string $name Backup filename (without extension)
     * @return RemoteCommandOutput
     */
    public function saveBackup(string $name): RemoteCommandOutput
    {
        $cmd = '/system backup save name=' . self::escapeMikrotikArgument($name);
        return $this->remoteExec($cmd);
    }

    /**
     * Load a system backup file
     *
     * @param string $name Backup filename (without extension)
     * @return RemoteCommandOutput
     */
    public function loadBackup(string $name): RemoteCommandOutput
    {
        $cmd = '/system backup load name=' . self::escapeMikrotikArgument($name);
        return $this->remoteExec($cmd);
    }

    /**
     * Get file list on the device
     *
     * @return RemoteCommandOutput
     */
    public function getFiles(): RemoteCommandOutput
    {
        return $this->remoteExec('/file print');
    }

    /**
     * Remove a file from the device
     *
     * @param string $name Filename
     * @return RemoteCommandOutput
     */
    public function removeFile(string $name): RemoteCommandOutput
    {
        $cmd = '/file remove [find name=' . self::escapeMikrotikArgument($name) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Get current active connections/connections table
     *
     * @return RemoteCommandOutput
     */
    public function getConnections(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip firewall connection print');
    }

    /**
     * Get DHCP client leases on WAN interfaces
     *
     * @return RemoteCommandOutput
     */
    public function getDhcpClients(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip dhcp-client print');
    }

    /**
     * Get MNDP (MikroTik Neighbor Discovery Protocol) neighbors
     *
     * @return RemoteCommandOutput
     */
    public function getNeighbors(): RemoteCommandOutput
    {
        return $this->remoteExec('/ip neighbor print');
    }

    /**
     * Get bandwidth test server status
     *
     * @return RemoteCommandOutput
     */
    public function getBandwidthServer(): RemoteCommandOutput
    {
        return $this->remoteExec('/tool bandwidth-server print');
    }

    /**
     * Get scheduler entries
     *
     * @return RemoteCommandOutput
     */
    public function getScheduler(): RemoteCommandOutput
    {
        return $this->remoteExec('/system scheduler print');
    }

    /**
     * Add a scheduler entry
     *
     * @param string $name Entry name
     * @param string $interval Execution interval (e.g. "00:05:00" for 5min)
     * @param string $onEvent Script/path to run
     * @param string $startTime Start time (e.g. "startup")
     * @return RemoteCommandOutput
     */
    public function addScheduler(string $name, string $interval, string $onEvent, string $startTime = 'startup'): RemoteCommandOutput
    {
        $cmd = '/system scheduler add name=' . self::escapeMikrotikArgument($name)
            . ' interval=' . self::escapeMikrotikArgument($interval)
            . ' on-event=' . self::escapeMikrotikArgument($onEvent)
            . ' start-time=' . self::escapeMikrotikArgument($startTime);
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a scheduler entry
     *
     * @param string $name Entry name
     * @return RemoteCommandOutput
     */
    public function removeScheduler(string $name): RemoteCommandOutput
    {
        $cmd = '/system scheduler remove [find name=' . self::escapeMikrotikArgument($name) . ']';
        return $this->remoteExec($cmd);
    }

    /**
     * Execute an arbitrary RouterOS command
     *
     * @param string $command Any RouterOS CLI command (with / prefix)
     * @return RemoteCommandOutput
     */
    public function exec(string $command): RemoteCommandOutput
    {
        return $this->remoteExec($command);
    }

    /**
     * Escape a value for safe use in RouterOS CLI
     *
     * RouterOS uses double quotes for values containing spaces or special characters.
     * Single quotes are literal characters and must not be used.
     *
     * @param string $value The value to escape
     * @return string
     */
    protected static function escapeMikrotikArgument(string $value): string
    {
        if ($value === '') {
            return '""';
        }

        if (preg_match('/^[a-zA-Z0-9.\/:@_\-+]+$/', $value)) {
            return $value;
        }

        $escaped = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
        return '"' . $escaped . '"';
    }
}
