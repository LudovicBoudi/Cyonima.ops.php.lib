<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Palo Alto Networks PAN-OS network equipment operations
 *
 * Provides methods for configuring Palo Alto Networks firewalls
 * via SSH using the PAN-OS CLI syntax.
 *
 * PAN-OS uses `configure` to enter configuration mode with `set`/`delete`
 * commands, and `commit` to apply changes. Operational mode commands use
 * `show`, `request`, `ping`, `traceroute`, etc.
 *
 * @see https://docs.paloaltonetworks.com/pan-os
 */
class PaloAltoOps extends AbstractOps
{
    /**
     * Get firewall system information
     *
     * @return RemoteCommandOutput
     */
    public function getSystemInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('show system info');
    }

    /**
     * Get PAN-OS version
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('show system info | match sw-version');
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
     * Get system resource usage (CPU, memory, disk)
     *
     * @return RemoteCommandOutput
     */
    public function getResources(): RemoteCommandOutput
    {
        return $this->remoteExec('show system resources');
    }

    /**
     * Get system clock
     *
     * @return RemoteCommandOutput
     */
    public function getClock(): RemoteCommandOutput
    {
        return $this->remoteExec('show clock');
    }

    /**
     * Set the firewall hostname
     *
     * @param string $hostname New hostname
     * @return RemoteCommandOutput
     */
    public function setHostname(string $hostname): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set deviceconfig system hostname ' . self::escapeShellArgument($hostname)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Set the firewall domain name
     *
     * @param string $domain Domain name
     * @return RemoteCommandOutput
     */
    public function setDomainName(string $domain): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set deviceconfig system domain ' . self::escapeShellArgument($domain)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Set the login banner
     *
     * @param string $message Banner message text
     * @return RemoteCommandOutput
     */
    public function setBanner(string $message): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set deviceconfig system login banner ' . self::escapeShellArgument($message)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Set DNS servers
     *
     * @param array<string> $servers List of DNS server IPs
     * @return RemoteCommandOutput
     */
    public function setDnsServers(array $servers): RemoteCommandOutput
    {
        $cmd = 'configure';
        foreach ($servers as $server) {
            $cmd .= ' && set deviceconfig system dns-setting servers ' . self::escapeShellArgument($server);
        }
        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Set NTP server
     *
     * @param string $server NTP server hostname or IP
     * @return RemoteCommandOutput
     */
    public function setNtpServer(string $server): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set deviceconfig system ntp-servers primary-ntp-server ' . self::escapeShellArgument($server)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Set the default gateway for management interface
     *
     * @param string $gateway Gateway IP address
     * @return RemoteCommandOutput
     */
    public function setDefaultGateway(string $gateway): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set deviceconfig system default-gateway ' . self::escapeShellArgument($gateway)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Reboot the firewall
     *
     * @return RemoteCommandOutput
     */
    public function reboot(): RemoteCommandOutput
    {
        return $this->remoteExec('request system reboot');
    }

    /**
     * Shutdown the firewall
     *
     * @return RemoteCommandOutput
     */
    public function shutdown(): RemoteCommandOutput
    {
        return $this->remoteExec('request system shutdown');
    }

    /**
     * Get list of all interfaces (brief)
     *
     * @return RemoteCommandOutput
     */
    public function getInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('show interfaces all');
    }

    /**
     * Get detailed information for a specific interface
     *
     * @param string $interface Interface name (e.g. "ethernet1/1")
     * @return RemoteCommandOutput
     */
    public function getInterfaceDetail(string $interface): RemoteCommandOutput
    {
        $cmd = 'show interface ' . self::escapeShellArgument($interface);
        return $this->remoteExec($cmd);
    }

    /**
     * Set IP address on a layer3 interface
     *
     * @param string $interface Interface name (e.g. "ethernet1/1")
     * @param string $ipCidr IP address with CIDR prefix (e.g. "10.0.0.1/24")
     * @return RemoteCommandOutput
     */
    public function setInterfaceIp(string $interface, string $ipCidr): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set network interface ethernet ' . self::escapeShellArgument($interface)
            . ' layer3 ip ' . self::escapeShellArgument($ipCidr)
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
            . ' && delete network interface ethernet ' . self::escapeShellArgument($interface) . ' layer3 ip'
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Set the zone assigned to an interface
     *
     * @param string $interface Interface name
     * @param string $zone Zone name
     * @return RemoteCommandOutput
     */
    public function setInterfaceZone(string $interface, string $zone): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set network interface ethernet ' . self::escapeShellArgument($interface)
            . ' zone ' . self::escapeShellArgument($zone)
            . ' && commit';
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
        $cmd = 'configure'
            . ' && set network interface ethernet ' . self::escapeShellArgument($interface)
            . ' comment ' . self::escapeShellArgument($comment)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Create a zone
     *
     * @param string $zoneName Zone name
     * @param string $type Zone type (layer3, layer2, virtual-wire, tap, tunnel)
     * @param array<string> $interfaces Interfaces to add to the zone
     * @return RemoteCommandOutput
     */
    public function createZone(string $zoneName, string $type = 'layer3', array $interfaces = []): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set zone ' . self::escapeShellArgument($zoneName)
            . ' network ' . $type;

        foreach ($interfaces as $iface) {
            $cmd .= ' && set zone ' . self::escapeShellArgument($zoneName) . ' network ' . $type . ' ' . self::escapeShellArgument($iface);
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Get the routing table
     *
     * @return RemoteCommandOutput
     */
    public function getRoutes(): RemoteCommandOutput
    {
        return $this->remoteExec('show routing route');
    }

    /**
     * Get route for a specific prefix
     *
     * @param string $prefix IP prefix (e.g. "10.0.0.0/8")
     * @return RemoteCommandOutput
     */
    public function getRouteForPrefix(string $prefix): RemoteCommandOutput
    {
        $cmd = 'show routing route ' . self::escapeShellArgument($prefix);
        return $this->remoteExec($cmd);
    }

    /**
     * Get ARP table
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
     * @param string $name Route name
     * @param string $destination Destination prefix (e.g. "10.0.0.0/8")
     * @param string $nexthop Next-hop IP address
     * @param string|null $virtualRouter Virtual router name (default: "default")
     * @return RemoteCommandOutput
     */
    public function addStaticRoute(string $name, string $destination, string $nexthop, ?string $virtualRouter = null): RemoteCommandOutput
    {
        $vr = $virtualRouter ?? 'default';
        $cmd = 'configure'
            . ' && set network virtual-router ' . self::escapeShellArgument($vr)
            . ' routing-table ip static-route ' . self::escapeShellArgument($name)
            . ' nexthop ip-address ' . self::escapeShellArgument($nexthop)
            . ' destination ' . self::escapeShellArgument($destination)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Remove a static route
     *
     * @param string $name Route name
     * @param string|null $virtualRouter Virtual router name (default: "default")
     * @return RemoteCommandOutput
     */
    public function removeStaticRoute(string $name, ?string $virtualRouter = null): RemoteCommandOutput
    {
        $vr = $virtualRouter ?? 'default';
        $cmd = 'configure'
            . ' && delete network virtual-router ' . self::escapeShellArgument($vr)
            . ' routing-table ip static-route ' . self::escapeShellArgument($name)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Get all configured security rules
     *
     * @return RemoteCommandOutput
     */
    public function getSecurityRules(): RemoteCommandOutput
    {
        return $this->remoteExec('show rulebase security rules');
    }

    /**
     * Add a security rule
     *
     * @param string $name Rule name
     * @param string $action Action (allow, deny, drop, reset-client, reset-server)
     * @param string $from Source zone (e.g. "untrust")
     * @param string $to Destination zone (e.g. "trust")
     * @param string $source Source addresses (e.g. "any" or "192.168.1.0/24")
     * @param string $destination Destination addresses (e.g. "any" or "10.0.0.1/32")
     * @param string|null $application Application (e.g. "any", "ssl", "web-browsing")
     * @param string|null $service Service (e.g. "any", "application-default", "tcp-443")
     * @param string|null $description Optional description
     * @return RemoteCommandOutput
     */
    public function addSecurityRule(
        string $name,
        string $action,
        string $from = 'any',
        string $to = 'any',
        string $source = 'any',
        string $destination = 'any',
        ?string $application = null,
        ?string $service = null,
        ?string $description = null
    ): RemoteCommandOutput {
        $cmd = 'configure'
            . ' && set rulebase security rules ' . self::escapeShellArgument($name)
            . ' action ' . self::escapeShellArgument($action)
            . ' && set rulebase security rules ' . self::escapeShellArgument($name)
            . ' from ' . self::escapeShellArgument($from)
            . ' && set rulebase security rules ' . self::escapeShellArgument($name)
            . ' to ' . self::escapeShellArgument($to)
            . ' && set rulebase security rules ' . self::escapeShellArgument($name)
            . ' source ' . self::escapeShellArgument($source)
            . ' && set rulebase security rules ' . self::escapeShellArgument($name)
            . ' destination ' . self::escapeShellArgument($destination);

        if ($application !== null) {
            $cmd .= ' && set rulebase security rules ' . self::escapeShellArgument($name)
                . ' application ' . self::escapeShellArgument($application);
        }
        if ($service !== null) {
            $cmd .= ' && set rulebase security rules ' . self::escapeShellArgument($name)
                . ' service ' . self::escapeShellArgument($service);
        }
        if ($description !== null) {
            $cmd .= ' && set rulebase security rules ' . self::escapeShellArgument($name)
                . ' description ' . self::escapeShellArgument($description);
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a security rule
     *
     * @param string $name Rule name
     * @return RemoteCommandOutput
     */
    public function deleteSecurityRule(string $name): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete rulebase security rules ' . self::escapeShellArgument($name)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Disable a security rule
     *
     * @param string $name Rule name
     * @return RemoteCommandOutput
     */
    public function disableSecurityRule(string $name): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set rulebase security rules ' . self::escapeShellArgument($name)
            . ' disabled yes'
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Enable a security rule
     *
     * @param string $name Rule name
     * @return RemoteCommandOutput
     */
    public function enableSecurityRule(string $name): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete rulebase security rules ' . self::escapeShellArgument($name) . ' disabled'
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Move a security rule in the rulebase
     *
     * @param string $name Rule name to move
     * @param string $position Position: top, bottom, before <rule>, after <rule>
     * @param string|null $reference Reference rule name (required for before/after)
     * @return RemoteCommandOutput
     */
    public function moveSecurityRule(string $name, string $position, ?string $reference = null): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && move rulebase security rules ' . self::escapeShellArgument($name) . ' '
            . self::escapeShellArgument($position);

        if ($reference !== null && in_array($position, ['before', 'after'], true)) {
            $cmd .= ' ' . self::escapeShellArgument($reference);
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Get all NAT rules
     *
     * @return RemoteCommandOutput
     */
    public function getNatRules(): RemoteCommandOutput
    {
        return $this->remoteExec('show rulebase nat rules');
    }

    /**
     * Add a NAT rule
     *
     * @param string $name Rule name
     * @param string $natType NAT type (ipv4, ipv6, nat64, nptv6)
     * @param string $from Source zone
     * @param string $to Destination zone
     * @param string $source Source addresses
     * @param string $destination Destination addresses
     * @param string $service Service
     * @param string|null $sourceTranslation Source translation type (e.g. "dynamic-ip-and-port", "static-ip")
     * @param string|null $destinationTranslation Destination translation address
     * @return RemoteCommandOutput
     */
    public function addNatRule(
        string $name,
        string $natType = 'ipv4',
        string $from = 'any',
        string $to = 'any',
        string $source = 'any',
        string $destination = 'any',
        string $service = 'any',
        ?string $sourceTranslation = null,
        ?string $destinationTranslation = null
    ): RemoteCommandOutput {
        $cmd = 'configure'
            . ' && set rulebase nat rules ' . self::escapeShellArgument($name)
            . ' nat-type ' . self::escapeShellArgument($natType)
            . ' && set rulebase nat rules ' . self::escapeShellArgument($name)
            . ' from ' . self::escapeShellArgument($from)
            . ' && set rulebase nat rules ' . self::escapeShellArgument($name)
            . ' to ' . self::escapeShellArgument($to)
            . ' && set rulebase nat rules ' . self::escapeShellArgument($name)
            . ' source ' . self::escapeShellArgument($source)
            . ' && set rulebase nat rules ' . self::escapeShellArgument($name)
            . ' destination ' . self::escapeShellArgument($destination)
            . ' && set rulebase nat rules ' . self::escapeShellArgument($name)
            . ' service ' . self::escapeShellArgument($service);

        if ($sourceTranslation !== null) {
            $cmd .= ' && set rulebase nat rules ' . self::escapeShellArgument($name)
                . ' source-translation type ' . self::escapeShellArgument($sourceTranslation);
        }
        if ($destinationTranslation !== null) {
            $cmd .= ' && set rulebase nat rules ' . self::escapeShellArgument($name)
                . ' destination-translation translated-address ' . self::escapeShellArgument($destinationTranslation);
        }

        $cmd .= ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a NAT rule
     *
     * @param string $name Rule name
     * @return RemoteCommandOutput
     */
    public function deleteNatRule(string $name): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete rulebase nat rules ' . self::escapeShellArgument($name)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Get all address objects
     *
     * @return RemoteCommandOutput
     */
    public function getAddresses(): RemoteCommandOutput
    {
        return $this->remoteExec('show shared address');
    }

    /**
     * Create an address object
     *
     * @param string $name Address object name
     * @param string $type Address type (ip-netmask, ip-range, fqdn)
     * @param string $value Address value (e.g. "10.0.0.1/32", "10.0.0.1-10.0.0.10", "example.com")
     * @return RemoteCommandOutput
     */
    public function createAddress(string $name, string $type, string $value): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set shared address ' . self::escapeShellArgument($name)
            . ' ' . self::escapeShellArgument($type) . ' ' . self::escapeShellArgument($value)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete an address object
     *
     * @param string $name Address object name
     * @return RemoteCommandOutput
     */
    public function deleteAddress(string $name): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete shared address ' . self::escapeShellArgument($name)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Get all service objects
     *
     * @return RemoteCommandOutput
     */
    public function getServices(): RemoteCommandOutput
    {
        return $this->remoteExec('show shared service');
    }

    /**
     * Create a custom service object
     *
     * @param string $name Service name
     * @param string $protocol Protocol (tcp, udp)
     * @param string $port Port number or range (e.g. "80", "8000-9000")
     * @return RemoteCommandOutput
     */
    public function createService(string $name, string $protocol, string $port): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set shared service ' . self::escapeShellArgument($name)
            . ' protocol ' . self::escapeShellArgument($protocol)
            . ' port ' . self::escapeShellArgument($port)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a service object
     *
     * @param string $name Service name
     * @return RemoteCommandOutput
     */
    public function deleteService(string $name): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && delete shared service ' . self::escapeShellArgument($name)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Get system logs
     *
     * @param int $rows Number of log entries (default 100)
     * @return RemoteCommandOutput
     */
    public function getSystemLogs(int $rows = 100): RemoteCommandOutput
    {
        $cmd = 'show log system rows ' . $rows;
        return $this->remoteExec($cmd);
    }

    /**
     * Get traffic logs
     *
     * @param int $rows Number of log entries (default 100)
     * @return RemoteCommandOutput
     */
    public function getTrafficLogs(int $rows = 100): RemoteCommandOutput
    {
        $cmd = 'show log traffic rows ' . $rows;
        return $this->remoteExec($cmd);
    }

    /**
     * Get threat logs
     *
     * @param int $rows Number of log entries (default 100)
     * @return RemoteCommandOutput
     */
    public function getThreatLogs(int $rows = 100): RemoteCommandOutput
    {
        $cmd = 'show log threat rows ' . $rows;
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a syslog server for logging
     *
     * @param string $name Syslog profile name
     * @param string $server Syslog server IP
     * @param string $facility Syslog facility (local0-local7)
     * @return RemoteCommandOutput
     */
    public function setSyslogServer(string $name, string $server, string $facility = 'local0'): RemoteCommandOutput
    {
        $cmd = 'configure'
            . ' && set shared log-settings syslog ' . self::escapeShellArgument($name)
            . ' server ' . self::escapeShellArgument($server)
            . ' facility ' . self::escapeShellArgument($facility)
            . ' && commit';
        return $this->remoteExec($cmd);
    }

    /**
     * Commit the candidate configuration
     *
     * @param string|null $description Optional commit description
     * @return RemoteCommandOutput
     */
    public function commit(?string $description = null): RemoteCommandOutput
    {
        $cmd = 'commit';
        if ($description !== null) {
            $cmd .= ' description ' . self::escapeShellArgument($description);
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Force commit (bypass warnings)
     *
     * @param string|null $description Optional commit description
     * @return RemoteCommandOutput
     */
    public function commitForce(?string $description = null): RemoteCommandOutput
    {
        $cmd = 'commit force';
        if ($description !== null) {
            $cmd .= ' description ' . self::escapeShellArgument($description);
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Show configuration changes between candidate and running
     *
     * @return RemoteCommandOutput
     */
    public function showChanges(): RemoteCommandOutput
    {
        return $this->remoteExec('show config diff');
    }

    /**
     * Discard candidate configuration changes
     *
     * @return RemoteCommandOutput
     */
    public function discardConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('configure && discard && exit');
    }

    /**
     * Save the candidate configuration
     *
     * @return RemoteCommandOutput
     */
    public function saveConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('save config');
    }

    /**
     * Ping a remote host from the firewall
     *
     * @param string $host IP address or hostname
     * @param int $count Number of packets (default 5)
     * @return RemoteCommandOutput
     */
    public function ping(string $host, int $count = 5): RemoteCommandOutput
    {
        $cmd = 'ping host ' . self::escapeShellArgument($host) . ' count ' . $count;
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
     * Get active sessions
     *
     * @return RemoteCommandOutput
     */
    public function getSessions(): RemoteCommandOutput
    {
        return $this->remoteExec('show session all');
    }

    /**
     * Get active administrators
     *
     * @return RemoteCommandOutput
     */
    public function getAdmins(): RemoteCommandOutput
    {
        return $this->remoteExec('show admins');
    }

    /**
     * Get running jobs
     *
     * @return RemoteCommandOutput
     */
    public function getJobs(): RemoteCommandOutput
    {
        return $this->remoteExec('show jobs all');
    }

    /**
     * Get PAN-OS license information
     *
     * @return RemoteCommandOutput
     */
    public function getLicenses(): RemoteCommandOutput
    {
        return $this->remoteExec('show licenses');
    }

    /**
     * Execute an arbitrary PAN-OS command in operational mode
     *
     * @param string $command Any PAN-OS operational mode command
     * @return RemoteCommandOutput
     */
    public function exec(string $command): RemoteCommandOutput
    {
        return $this->remoteExec($command);
    }
}
