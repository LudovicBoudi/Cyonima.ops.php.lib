<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Cisco IOS/IOS-XE network equipment operations
 *
 * Provides methods for configuring Cisco switches and routers via SSH using
 * IOS CLI syntax.
 *
 * CLI handling notes:
 * - Cisco IOS is not a POSIX shell: arguments are NOT shell-quoted. The only
 *   escaping applied by escapeCiscoArgument() is to reject line breaks, which
 *   would otherwise inject additional CLI commands.
 * - IOS has no `&&` operator. Configuration is pushed as a sequence of newline
 *   separated lines (see sendConfig()), entering configuration mode, applying
 *   the lines, then leaving with `end` and `write memory`.
 *
 * This assumes the device accepts newline-separated input on the SSH exec
 * channel; behaviour should be validated against the target IOS version.
 */
class CiscoOps extends AbstractOps
{
    /**
     * Enter enable mode (privileged exec)
     *
     * Note: on real IOS devices the `enable` command prompts for the password
     * interactively; passing it as an argument works only on devices/emulators
     * that accept it inline.
     *
     * @param string|null $enableSecret Optional enable secret password
     * @return RemoteCommandOutput
     */
    public function enterEnableMode(?string $enableSecret = null): RemoteCommandOutput
    {
        if ($enableSecret !== null) {
            return $this->remoteExec('enable ' . self::escapeCiscoArgument($enableSecret));
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
        return $this->remoteExec('show running-config interface ' . self::escapeCiscoArgument($interface));
    }

    /**
     * Get interface status and counters
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function getInterfaceStatus(string $interface): RemoteCommandOutput
    {
        return $this->remoteExec('show interfaces ' . self::escapeCiscoArgument($interface));
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
        return $this->remoteExec('ping ' . self::escapeCiscoArgument($host) . ' repeat ' . $count);
    }

    /**
     * Traceroute from the device
     *
     * @param string $host IP address or hostname
     * @return RemoteCommandOutput
     */
    public function traceroute(string $host): RemoteCommandOutput
    {
        return $this->remoteExec('traceroute ' . self::escapeCiscoArgument($host));
    }

    /**
     * Set the device hostname
     *
     * @param string $hostname New hostname
     * @return RemoteCommandOutput
     */
    public function setHostname(string $hostname): RemoteCommandOutput
    {
        return $this->sendConfig([
            'hostname ' . self::escapeCiscoArgument($hostname),
        ]);
    }

    /**
     * Set a MOTD banner
     *
     * The `^` character is used as the banner delimiter and therefore must not
     * appear in the message.
     *
     * @param string $message Banner message text
     * @return RemoteCommandOutput
     */
    public function setBannerMotd(string $message): RemoteCommandOutput
    {
        return $this->sendConfig([
            'banner motd ^' . self::escapeCiscoArgument($message) . '^',
        ]);
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
        return $this->sendConfig([
            'username ' . self::escapeCiscoArgument($username)
                . ' privilege ' . $privilege
                . ' secret ' . self::escapeCiscoArgument($password),
        ]);
    }

    /**
     * Delete a local user account
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        return $this->sendConfig([
            'no username ' . self::escapeCiscoArgument($username),
        ]);
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
        return $this->sendConfig([
            'ip domain-name ' . self::escapeCiscoArgument($domainName),
            'crypto key generate rsa modulus ' . $keySize,
            'ip ssh version ' . $sshVersion,
            'line vty 0 4',
            'transport input ssh',
            'login local',
            'exit',
        ]);
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
        return $this->sendConfig([
            'vlan ' . $vlanId,
            'name ' . self::escapeCiscoArgument($vlanName),
            'exit',
        ]);
    }

    /**
     * Delete a VLAN
     *
     * @param int $vlanId VLAN ID
     * @return RemoteCommandOutput
     */
    public function deleteVlan(int $vlanId): RemoteCommandOutput
    {
        return $this->sendConfig([
            'no vlan ' . $vlanId,
        ]);
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
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'switchport mode access',
            'switchport access vlan ' . $vlanId,
            'exit',
        ]);
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
        $lines = [
            'interface ' . self::escapeCiscoArgument($interface),
            'switchport mode trunk',
            'switchport trunk native vlan ' . self::escapeCiscoArgument($nativeVlan),
        ];

        if ($allowedVlans !== null) {
            $lines[] = 'switchport trunk allowed vlan ' . self::escapeCiscoArgument($allowedVlans);
        }

        $lines[] = 'exit';

        return $this->sendConfig($lines);
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
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'description ' . self::escapeCiscoArgument($description),
            'exit',
        ]);
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
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'speed ' . self::escapeCiscoArgument($speed),
            'duplex ' . self::escapeCiscoArgument($duplex),
            'exit',
        ]);
    }

    /**
     * Administratively disable an interface (shutdown)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function shutdownInterface(string $interface): RemoteCommandOutput
    {
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'shutdown',
            'exit',
        ]);
    }

    /**
     * Administratively enable an interface (no shutdown)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function noShutdownInterface(string $interface): RemoteCommandOutput
    {
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'no shutdown',
            'exit',
        ]);
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
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'ip address ' . self::escapeCiscoArgument($ipAddress) . ' ' . self::escapeCiscoArgument($netmask),
            'no shutdown',
            'exit',
        ]);
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
        return $this->sendConfig([
            'interface vlan ' . $vlanId,
            'ip address ' . self::escapeCiscoArgument($ipAddress) . ' ' . self::escapeCiscoArgument($netmask),
            'no shutdown',
            'exit',
        ]);
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
        $route = 'ip route ' . self::escapeCiscoArgument($network) . ' ' . self::escapeCiscoArgument($netmask)
            . ' ' . self::escapeCiscoArgument($nextHop);

        if ($distance !== null) {
            $route .= ' ' . $distance;
        }

        return $this->sendConfig([$route]);
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
        return $this->sendConfig([
            'no ip route ' . self::escapeCiscoArgument($network) . ' ' . self::escapeCiscoArgument($netmask)
                . ' ' . self::escapeCiscoArgument($nextHop),
        ]);
    }

    /**
     * Set the default gateway
     *
     * @param string $gateway Gateway IP address
     * @return RemoteCommandOutput
     */
    public function setDefaultGateway(string $gateway): RemoteCommandOutput
    {
        return $this->sendConfig([
            'ip default-gateway ' . self::escapeCiscoArgument($gateway),
        ]);
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
        return $this->sendConfig([
            'access-list ' . $aclNumber . ' ' . self::escapeCiscoArgument($action) . ' ' . self::escapeCiscoArgument($source),
        ]);
    }

    /**
     * Remove an IP access-list
     *
     * @param int $aclNumber ACL number
     * @return RemoteCommandOutput
     */
    public function removeAcl(int $aclNumber): RemoteCommandOutput
    {
        return $this->sendConfig([
            'no access-list ' . $aclNumber,
        ]);
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
        $entry = self::escapeCiscoArgument($action) . ' ' . self::escapeCiscoArgument($protocol)
            . ' ' . self::escapeCiscoArgument($source)
            . ' ' . self::escapeCiscoArgument($destination);

        if ($dstPort !== null) {
            $entry .= ' eq ' . $dstPort;
        }

        return $this->sendConfig([
            'ip access-list extended ' . self::escapeCiscoArgument($aclName),
            $entry,
            'exit',
        ]);
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
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'ip access-group ' . self::escapeCiscoArgument($aclName) . ' in',
            'exit',
        ]);
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
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'ip access-group ' . self::escapeCiscoArgument($aclName) . ' out',
            'exit',
        ]);
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
        return $this->sendConfig([
            'ip flow-export destination ' . self::escapeCiscoArgument($collectorIp) . ' ' . $collectorPort,
            'ip flow-export source ' . self::escapeCiscoArgument($sourceInterface),
            'ip flow-export version 9',
            'ip flow-cache timeout active 1',
        ]);
    }

    /**
     * Enable NetFlow on a specific interface (ingress)
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function enableInterfaceNetflow(string $interface): RemoteCommandOutput
    {
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'ip flow ingress',
            'ip flow egress',
            'exit',
        ]);
    }

    /**
     * Configure NTP server
     *
     * @param string $server NTP server IP or hostname
     * @return RemoteCommandOutput
     */
    public function setNtpServer(string $server): RemoteCommandOutput
    {
        return $this->sendConfig([
            'ntp server ' . self::escapeCiscoArgument($server),
        ]);
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
        $line = 'snmp-server community ' . self::escapeCiscoArgument($community) . ' ' . self::escapeCiscoArgument($access);

        if ($acl !== null) {
            $line .= ' ' . self::escapeCiscoArgument($acl);
        }

        return $this->sendConfig([$line]);
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
        return $this->sendConfig([
            'logging host ' . self::escapeCiscoArgument($server),
            'logging trap ' . self::escapeCiscoArgument($level),
            'logging on',
        ]);
    }

    /**
     * Enable CDP globally
     *
     * @return RemoteCommandOutput
     */
    public function enableCdp(): RemoteCommandOutput
    {
        return $this->sendConfig(['cdp run']);
    }

    /**
     * Disable CDP globally
     *
     * @return RemoteCommandOutput
     */
    public function disableCdp(): RemoteCommandOutput
    {
        return $this->sendConfig(['no cdp run']);
    }

    /**
     * Enable LLDP globally
     *
     * @return RemoteCommandOutput
     */
    public function enableLldp(): RemoteCommandOutput
    {
        return $this->sendConfig(['lldp run']);
    }

    /**
     * Disable LLDP globally
     *
     * @return RemoteCommandOutput
     */
    public function disableLldp(): RemoteCommandOutput
    {
        return $this->sendConfig(['no lldp run']);
    }

    /**
     * Configure an interface as DHCP client
     *
     * @param string $interface Interface name
     * @return RemoteCommandOutput
     */
    public function setInterfaceDhcpClient(string $interface): RemoteCommandOutput
    {
        return $this->sendConfig([
            'interface ' . self::escapeCiscoArgument($interface),
            'ip address dhcp',
            'no shutdown',
            'exit',
        ]);
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
        $lines = [
            'interface port-channel ' . $channelNumber,
            'exit',
        ];

        foreach ($memberInterfaces as $iface) {
            $lines[] = 'interface ' . self::escapeCiscoArgument($iface);
            $lines[] = 'channel-group ' . $channelNumber . ' mode ' . self::escapeCiscoArgument($mode);
            $lines[] = 'exit';
        }

        return $this->sendConfig($lines);
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

    /**
     * Send a configuration sequence to the device
     *
     * Wraps the given configuration lines with `configure terminal` ... `end`
     * and `write memory`, sending them as newline-separated input on the SSH
     * exec channel (IOS has no `&&` command chaining).
     *
     * @param array<string> $lines Configuration lines to apply
     * @return RemoteCommandOutput
     */
    protected function sendConfig(array $lines): RemoteCommandOutput
    {
        $commands = array_merge(
            ['configure terminal'],
            $lines,
            ['end', 'write memory']
        );

        return $this->remoteExec(implode("\n", $commands));
    }

    /**
     * Escape a value for safe use in a Cisco IOS CLI command
     *
     * Cisco IOS is not a POSIX shell and does not use shell-style quoting, so
     * values are passed as-is. The one real injection vector is a line break,
     * which would terminate the current command and inject a new one; such
     * values are rejected.
     *
     * @param string $value The value to include in a command
     * @return string The value, unchanged if safe
     * @throws \InvalidArgumentException if the value contains a line break
     */
    protected static function escapeCiscoArgument(string $value): string
    {
        if (preg_match('/[\r\n]/', $value)) {
            throw new \InvalidArgumentException(
                'Invalid Cisco CLI argument: line breaks are not allowed.'
            );
        }

        return $value;
    }
}
