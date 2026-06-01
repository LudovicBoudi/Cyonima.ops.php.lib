<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Fortinet FortiGate / FortiOS network equipment operations
 *
 * Provides methods for configuring Fortinet firewalls via SSH using
 * the FortiOS CLI syntax.
 *
 * FortiOS uses `config ... edit ...` to enter configuration contexts,
 * `set`/`unset` to modify parameters, and `end` to exit.
 * Operational commands use `get`, `show`, `execute`, and `diagnose`.
 *
 * @see https://docs.fortinet.com/document/fortigate/6.0.0/cli-reference
 */
class FortinetOps extends AbstractOps
{
    /**
     * Get system status (version, uptime, serial, etc.)
     *
     * @return RemoteCommandOutput
     */
    public function getSystemStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('get system status');
    }

    /**
     * Get FortiOS version
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('get system status | grep Version');
    }

    /**
     * Get system performance (CPU, memory, sessions)
     *
     * @return RemoteCommandOutput
     */
    public function getPerformance(): RemoteCommandOutput
    {
        return $this->remoteExec('get system performance status');
    }

    /**
     * Get HA status (if configured)
     *
     * @return RemoteCommandOutput
     */
    public function getHaStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('get system ha status');
    }

    /**
     * Get license information
     *
     * @return RemoteCommandOutput
     */
    public function getLicenses(): RemoteCommandOutput
    {
        return $this->remoteExec('get system license');
    }

    /**
     * Set the device hostname
     *
     * @param string $hostname New hostname
     * @return RemoteCommandOutput
     */
    public function setHostname(string $hostname): RemoteCommandOutput
    {
        $cmd = 'config system global'
            . ' && set hostname ' . self::escapeShellArgument($hostname)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure DNS servers
     *
     * @param string $primary Primary DNS server
     * @param string|null $secondary Secondary DNS server
     * @return RemoteCommandOutput
     */
    public function setDnsServers(string $primary, ?string $secondary = null): RemoteCommandOutput
    {
        $cmd = 'config system dns'
            . ' && set primary ' . self::escapeShellArgument($primary);
        if ($secondary !== null) {
            $cmd .= ' && set secondary ' . self::escapeShellArgument($secondary);
        }
        $cmd .= ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Configure NTP server
     *
     * @param string $server NTP server hostname
     * @param string|null $server2 Optional second NTP server
     * @return RemoteCommandOutput
     */
    public function setNtpServer(string $server, ?string $server2 = null): RemoteCommandOutput
    {
        $cmd = 'config system ntp'
            . ' && set ntpsync enable'
            . ' && set server ' . self::escapeShellArgument($server);
        if ($server2 !== null) {
            $cmd .= ' && set server ' . self::escapeShellArgument($server2);
        }
        $cmd .= ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get NTP configuration
     *
     * @return RemoteCommandOutput
     */
    public function getNtpConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('get system ntp');
    }

    /**
     * Reboot the firewall
     *
     * @return RemoteCommandOutput
     */
    public function reboot(): RemoteCommandOutput
    {
        return $this->remoteExec('execute reboot');
    }

    /**
     * Shutdown the firewall
     *
     * @return RemoteCommandOutput
     */
    public function shutdown(): RemoteCommandOutput
    {
        return $this->remoteExec('execute shutdown');
    }

    /**
     * Factory reset the firewall
     *
     * @return RemoteCommandOutput
     */
    public function factoryReset(): RemoteCommandOutput
    {
        return $this->remoteExec('execute factoryreset');
    }

    /**
     * Get all interfaces (brief)
     *
     * @return RemoteCommandOutput
     */
    public function getInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('get system interface');
    }

    /**
     * Get detailed interface information
     *
     * @param string $interface Interface name (e.g. "port1", "wan1")
     * @return RemoteCommandOutput
     */
    public function getInterfaceDetail(string $interface): RemoteCommandOutput
    {
        $cmd = 'show system interface ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Set a static IP address on an interface
     *
     * @param string $interface Interface name
     * @param string $ipAddress IP address
     * @param string $netmask Subnet mask
     * @param string|null $allowAccess Allowed management access (e.g. "ping https ssh")
     * @return RemoteCommandOutput
     */
    public function setInterfaceIp(string $interface, string $ipAddress, string $netmask, ?string $allowAccess = null): RemoteCommandOutput
    {
        $cmd = 'config system interface'
            . ' && edit ' . self::escapeShellArgument($interface)
            . ' && set mode static'
            . ' && set ip ' . self::escapeShellArgument($ipAddress) . ' ' . self::escapeShellArgument($netmask);
        if ($allowAccess !== null) {
            $cmd .= ' && set allowaccess ' . self::escapeShellArgument($allowAccess);
        }
        $cmd .= ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Set interface mode to DHCP client
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function setInterfaceDhcp(string $interface): RemoteCommandOutput
    {
        $cmd = 'config system interface'
            . ' && edit ' . self::escapeShellArgument($interface)
            . ' && set mode dhcp'
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Set allowed management access on an interface
     *
     * @param string $interface Interface name
     * @param string $access Access methods (e.g. "ping https ssh snmp")
     * @return RemoteCommandOutput
     */
    public function setInterfaceAllowAccess(string $interface, string $access): RemoteCommandOutput
    {
        $cmd = 'config system interface'
            . ' && edit ' . self::escapeShellArgument($interface)
            . ' && set allowaccess ' . self::escapeShellArgument($access)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Set interface description / alias
     *
     * @param string $interface Interface name
     * @param string $description Description text
     * @return RemoteCommandOutput
     */
    public function setInterfaceDescription(string $interface, string $description): RemoteCommandOutput
    {
        $cmd = 'config system interface'
            . ' && edit ' . self::escapeShellArgument($interface)
            . ' && set alias ' . self::escapeShellArgument($description)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable an interface (set status up)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function enableInterface(string $interface): RemoteCommandOutput
    {
        $cmd = 'config system interface'
            . ' && edit ' . self::escapeShellArgument($interface)
            . ' && set status up'
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable an interface (set status down)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function disableInterface(string $interface): RemoteCommandOutput
    {
        $cmd = 'config system interface'
            . ' && edit ' . self::escapeShellArgument($interface)
            . ' && set status down'
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a VLAN subinterface
     *
     * @param int $vlanId VLAN ID (1-4094)
     * @param string $interface Physical interface
     * @param int|null $vlanif VLAN interface number (defaults to vlanId)
     * @return RemoteCommandOutput
     */
    public function createVlan(int $vlanId, string $interface, ?int $vlanif = null): RemoteCommandOutput
    {
        $vlanName = $interface . '_vlan' . ($vlanif ?? $vlanId);
        $cmd = 'config system interface'
            . ' && edit ' . self::escapeShellArgument($vlanName)
            . ' && set vlanid ' . $vlanId
            . ' && set interface ' . self::escapeShellArgument($interface)
            . ' && set type vlan'
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get the routing table
     *
     * @return RemoteCommandOutput
     */
    public function getRoutes(): RemoteCommandOutput
    {
        return $this->remoteExec('get router info routing-table all');
    }

    /**
     * Get route for a specific prefix
     *
     * @param string $prefix IP prefix (e.g. "10.0.0.0/8")
     * @return RemoteCommandOutput
     */
    public function getRouteForPrefix(string $prefix): RemoteCommandOutput
    {
        $cmd = 'get router info routing-table details ' . self::escapeShellArgument($prefix);
        return $this->remoteExec($cmd);
    }

    /**
     * Get the ARP table
     *
     * @return RemoteCommandOutput
     */
    public function getArpTable(): RemoteCommandOutput
    {
        return $this->remoteExec('get system arp');
    }

    /**
     * Add a static route
     *
     * @param int $routeId Route ID (auto-increment if 0)
     * @param string $destination Destination prefix (e.g. "10.0.0.0/8")
     * @param string $gateway Gateway IP address
     * @param string $device Outbound interface (e.g. "port1")
     * @return RemoteCommandOutput
     */
    public function addStaticRoute(int $routeId, string $destination, string $gateway, string $device): RemoteCommandOutput
    {
        [$dstIp, $dstMask] = $this->parsePrefix($destination);

        $cmd = 'config router static'
            . ' && edit ' . $routeId
            . ' && set dst ' . self::escapeShellArgument($dstIp) . ' ' . self::escapeShellArgument($dstMask)
            . ' && set gateway ' . self::escapeShellArgument($gateway)
            . ' && set device ' . self::escapeShellArgument($device)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a static route
     *
     * @param int $routeId Route ID
     * @return RemoteCommandOutput
     */
    public function removeStaticRoute(int $routeId): RemoteCommandOutput
    {
        $cmd = 'config router static'
            . ' && delete ' . $routeId
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Set the default gateway (via static route)
     *
     * @param string $gateway Gateway IP address
     * @param string $device Outbound interface
     * @param int $routeId Route ID (default 0 for auto)
     * @return RemoteCommandOutput
     */
    public function setDefaultGateway(string $gateway, string $device, int $routeId = 0): RemoteCommandOutput
    {
        $cmd = 'config router static'
            . ' && edit ' . $routeId
            . ' && set dst 0.0.0.0 0.0.0.0'
            . ' && set gateway ' . self::escapeShellArgument($gateway)
            . ' && set device ' . self::escapeShellArgument($device)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get all firewall policies
     *
     * @return RemoteCommandOutput
     */
    public function getPolicies(): RemoteCommandOutput
    {
        return $this->remoteExec('get firewall policy');
    }

    /**
     * Add a firewall policy
     *
     * @param int $policyId Policy ID (0 for auto)
     * @param string $name Policy name
     * @param string $action Action (accept, deny, ipsec)
     * @param string $srcintf Source interface/zone
     * @param string $dstintf Destination interface/zone
     * @param string $srcaddr Source address object name
     * @param string $dstaddr Destination address object name
     * @param string $service Service name (e.g. "ALL", "HTTPS", "SSH")
     * @param string $schedule Schedule name (default "always")
     * @param string|null $logtraffic Log setting (all, utm, forward, off)
     * @param string|null $comments Optional comment
     * @return RemoteCommandOutput
     */
    public function addPolicy(
        int $policyId,
        string $name,
        string $action = 'accept',
        string $srcintf = 'any',
        string $dstintf = 'any',
        string $srcaddr = 'all',
        string $dstaddr = 'all',
        string $service = 'ALL',
        string $schedule = 'always',
        ?string $logtraffic = null,
        ?string $comments = null
    ): RemoteCommandOutput {
        $cmd = 'config firewall policy'
            . ' && edit ' . $policyId
            . ' && set name ' . self::escapeShellArgument($name)
            . ' && set action ' . self::escapeShellArgument($action)
            . ' && set srcintf ' . self::escapeShellArgument($srcintf)
            . ' && set dstintf ' . self::escapeShellArgument($dstintf)
            . ' && set srcaddr ' . self::escapeShellArgument($srcaddr)
            . ' && set dstaddr ' . self::escapeShellArgument($dstaddr)
            . ' && set service ' . self::escapeShellArgument($service)
            . ' && set schedule ' . self::escapeShellArgument($schedule);

        if ($logtraffic !== null) {
            $cmd .= ' && set logtraffic ' . self::escapeShellArgument($logtraffic);
        }
        if ($comments !== null) {
            $cmd .= ' && set comments ' . self::escapeShellArgument($comments);
        }

        $cmd .= ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a firewall policy
     *
     * @param int $policyId Policy ID
     * @return RemoteCommandOutput
     */
    public function deletePolicy(int $policyId): RemoteCommandOutput
    {
        $cmd = 'config firewall policy'
            . ' && delete ' . $policyId
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable a firewall policy
     *
     * @param int $policyId Policy ID
     * @return RemoteCommandOutput
     */
    public function enablePolicy(int $policyId): RemoteCommandOutput
    {
        $cmd = 'config firewall policy'
            . ' && edit ' . $policyId
            . ' && set status enable'
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable a firewall policy
     *
     * @param int $policyId Policy ID
     * @return RemoteCommandOutput
     */
    public function disablePolicy(int $policyId): RemoteCommandOutput
    {
        $cmd = 'config firewall policy'
            . ' && edit ' . $policyId
            . ' && set status disable'
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get all address objects
     *
     * @return RemoteCommandOutput
     */
    public function getAddresses(): RemoteCommandOutput
    {
        return $this->remoteExec('get firewall address');
    }

    /**
     * Create an address object
     *
     * @param string $name Address name
     * @param string $subnet Subnet IP
     * @param string $netmask Netmask
     * @param string|null $comment Optional comment
     * @return RemoteCommandOutput
     */
    public function createAddress(string $name, string $subnet, string $netmask, ?string $comment = null): RemoteCommandOutput
    {
        $cmd = 'config firewall address'
            . ' && edit ' . self::escapeShellArgument($name)
            . ' && set subnet ' . self::escapeShellArgument($subnet) . ' ' . self::escapeShellArgument($netmask);
        if ($comment !== null) {
            $cmd .= ' && set comment ' . self::escapeShellArgument($comment);
        }
        $cmd .= ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete an address object
     *
     * @param string $name Address name
     * @return RemoteCommandOutput
     */
    public function deleteAddress(string $name): RemoteCommandOutput
    {
        $cmd = 'config firewall address'
            . ' && delete ' . self::escapeShellArgument($name)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get address groups
     *
     * @return RemoteCommandOutput
     */
    public function getAddressGroups(): RemoteCommandOutput
    {
        return $this->remoteExec('get firewall addrgrp');
    }

    /**
     * Create an address group
     *
     * @param string $name Group name
     * @param array<string> $members Member address object names
     * @return RemoteCommandOutput
     */
    public function createAddressGroup(string $name, array $members): RemoteCommandOutput
    {
        $cmd = 'config firewall addrgrp'
            . ' && edit ' . self::escapeShellArgument($name)
            . ' && set member';

        foreach ($members as $member) {
            $cmd .= ' ' . self::escapeShellArgument($member);
        }

        $cmd .= ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete an address group
     *
     * @param string $name Group name
     * @return RemoteCommandOutput
     */
    public function deleteAddressGroup(string $name): RemoteCommandOutput
    {
        $cmd = 'config firewall addrgrp'
            . ' && delete ' . self::escapeShellArgument($name)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get custom service objects
     *
     * @return RemoteCommandOutput
     */
    public function getServices(): RemoteCommandOutput
    {
        return $this->remoteExec('get firewall service custom');
    }

    /**
     * Create a custom service object
     *
     * @param string $name Service name
     * @param string $protocol Protocol (TCP, UDP, SCTP)
     * @param string $portRange Port or port range (e.g. "8080", "8000-9000")
     * @return RemoteCommandOutput
     */
    public function createService(string $name, string $protocol, string $portRange): RemoteCommandOutput
    {
        $cmd = 'config firewall service custom'
            . ' && edit ' . self::escapeShellArgument($name)
            . ' && set protocol ' . self::escapeShellArgument($protocol)
            . ' && set ' . strtolower($protocol) . '-portrange ' . self::escapeShellArgument($portRange)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a custom service object
     *
     * @param string $name Service name
     * @return RemoteCommandOutput
     */
    public function deleteService(string $name): RemoteCommandOutput
    {
        $cmd = 'config firewall service custom'
            . ' && delete ' . self::escapeShellArgument($name)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get admin users
     *
     * @return RemoteCommandOutput
     */
    public function getAdmins(): RemoteCommandOutput
    {
        return $this->remoteExec('get system admin');
    }

    /**
     * Create an admin user
     *
     * @param string $username Username
     * @param string $password Password
     * @param string $profile Administrator profile (e.g. "super_admin", "prof_admin", "read_only")
     * @return RemoteCommandOutput
     */
    public function createAdmin(string $username, string $password, string $profile = 'super_admin'): RemoteCommandOutput
    {
        $cmd = 'config system admin'
            . ' && edit ' . self::escapeShellArgument($username)
            . ' && set password ' . self::escapeShellArgument($password)
            . ' && set accprofile ' . self::escapeShellArgument($profile)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete an admin user
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function deleteAdmin(string $username): RemoteCommandOutput
    {
        $cmd = 'config system admin'
            . ' && delete ' . self::escapeShellArgument($username)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get SNMP community configuration
     *
     * @return RemoteCommandOutput
     */
    public function getSnmpCommunities(): RemoteCommandOutput
    {
        return $this->remoteExec('get system snmp community');
    }

    /**
     * Add an SNMP community
     *
     * @param int $communityId Community index
     * @param string $name Community name
     * @param string $queryV1 Enable SNMPv1 queries (enable/disable)
     * @param string $queryV2c Enable SNMPv2c queries (enable/disable)
     * @return RemoteCommandOutput
     */
    public function addSnmpCommunity(int $communityId, string $name, string $queryV1 = 'disable', string $queryV2c = 'enable'): RemoteCommandOutput
    {
        $cmd = 'config system snmp community'
            . ' && edit ' . $communityId
            . ' && set name ' . self::escapeShellArgument($name)
            . ' && set query-v1 ' . $queryV1
            . ' && set query-v2c ' . $queryV2c
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove an SNMP community
     *
     * @param int $communityId Community index
     * @return RemoteCommandOutput
     */
    public function deleteSnmpCommunity(int $communityId): RemoteCommandOutput
    {
        $cmd = 'config system snmp community'
            . ' && delete ' . $communityId
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Get syslog server configuration
     *
     * @return RemoteCommandOutput
     */
    public function getSyslogConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('get system log syslogd');
    }

    /**
     * Configure remote syslog server
     *
     * @param string $server Syslog server IP
     * @param string $facility Syslog facility (default "local0")
     * @return RemoteCommandOutput
     */
    public function setSyslogServer(string $server, string $facility = 'local0'): RemoteCommandOutput
    {
        $cmd = 'config system log syslogd'
            . ' && set status enable'
            . ' && set server ' . self::escapeShellArgument($server)
            . ' && set facility ' . self::escapeShellArgument($facility)
            . ' && end';
        return $this->remoteExec($cmd);
    }

    /**
     * Backup configuration via TFTP
     *
     * @param string $tftpServer TFTP server IP
     * @param string $filename Backup filename
     * @return RemoteCommandOutput
     */
    public function backupConfigTftp(string $tftpServer, string $filename): RemoteCommandOutput
    {
        $cmd = 'execute backup config tftp ' . self::escapeShellArgument($tftpServer) . ' ' . self::escapeShellArgument($filename);
        return $this->remoteExec($cmd);
    }

    /**
     * Restore configuration via TFTP
     *
     * @param string $tftpServer TFTP server IP
     * @param string $filename Backup filename
     * @return RemoteCommandOutput
     */
    public function restoreConfigTftp(string $tftpServer, string $filename): RemoteCommandOutput
    {
        $cmd = 'execute restore config tftp ' . self::escapeShellArgument($tftpServer) . ' ' . self::escapeShellArgument($filename);
        return $this->remoteExec($cmd);
    }

    /**
     * Get system hardware information
     *
     * @return RemoteCommandOutput
     */
    public function getHardwareInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('get hardware status');
    }

    /**
     * Get system diagnostics
     *
     * @return RemoteCommandOutput
     */
    public function getDiagnostics(): RemoteCommandOutput
    {
        return $this->remoteExec('diagnose sys device');
    }

    /**
     * Ping a remote host
     *
     * @param string $host IP address or hostname
     * @param int $count Number of packets (default 5)
     * @return RemoteCommandOutput
     */
    public function ping(string $host, int $count = 5): RemoteCommandOutput
    {
        $cmd = 'execute ping ' . self::escapeShellArgument($host) . ' ' . $count;
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
        return $this->remoteExec('execute traceroute ' . self::escapeShellArgument($host));
    }

    /**
     * Get active sessions / session table
     *
     * @return RemoteCommandOutput
     */
    public function getSessions(): RemoteCommandOutput
    {
        return $this->remoteExec('get system session status');
    }

    /**
     * Get system event logs (local logs)
     *
     * @param int $lines Number of lines (default 50)
     * @return RemoteCommandOutput
     */
    public function getEventLogs(int $lines = 50): RemoteCommandOutput
    {
        return $this->remoteExec('execute log display event ' . $lines);
    }

    /**
     * Get system attack logs
     *
     * @param int $lines Number of lines (default 50)
     * @return RemoteCommandOutput
     */
    public function getAttackLogs(int $lines = 50): RemoteCommandOutput
    {
        return $this->remoteExec('execute log display attack ' . $lines);
    }

    /**
     * Execute an arbitrary FortiOS command
     *
     * @param string $command Any FortiOS command (get, show, execute, etc.)
     * @return RemoteCommandOutput
     */
    public function exec(string $command): RemoteCommandOutput
    {
        return $this->remoteExec($command);
    }

    /**
     * Parse a CIDR prefix into IP address and netmask
     *
     * @param string $prefix CIDR notation prefix (e.g. "10.0.0.0/24")
     * @return array<string> [ipAddress, netmask]
     */
    private function parsePrefix(string $prefix): array
    {
        if (!str_contains($prefix, '/')) {
            return [$prefix, '255.255.255.255'];
        }

        [$ip, $cidr] = explode('/', $prefix, 2);

        if ($cidr === '0') {
            return [$ip, '0.0.0.0'];
        }

        $mask = long2ip((int) (0xFFFFFFFF << (32 - (int) $cidr)));

        return [$ip, $mask ?: '255.255.255.255'];
    }
}
