<?php

declare(strict_types=1);

namespace Cyonima\Ops\Network;

use Cyonima\Ops\AbstractOps;

/**
 * Cisco network equipment operations
 *
 * Provides methods for configuring Cisco switches and routers
 */
class CiscoOps extends AbstractOps
{
    /**
     * Configure basic host settings (hostname, banner, time)
     *
     * @param string $hostname Device hostname
     * @param string $location Device location for banner
     * @param string $time Current time to set
     * @return string Command output
     */
    public function configureHostBase(string $hostname, string $location, string $time): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "hostname $hostname\n";
        $cmd .= "prompt $hostname>\n";
        $cmd .= "banner motd c $location c\n";
        $cmd .= "clock set $time\n";
        return $this->remoteExec($cmd);
    }

    /**
     * Add a user account
     *
     * @param string $username Username
     * @param string $password User password
     * @return string Command output
     */
    public function addUser(string $username, string $password): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "username $username create\n";
        $cmd .= "username $username password $password\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Configure a VLAN with an interface
     *
     * @param string $vlanNumber VLAN ID
     * @param string $vlanName VLAN name
     * @param string $vlanInterface Interface to assign to VLAN
     * @return string Command output
     */
    public function configureVlan(string $vlanNumber, string $vlanName, string $vlanInterface): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "vlan $vlanNumber\n";
        $cmd .= "name $vlanName\n";
        $cmd .= "exit\n";
        $cmd .= "int $vlanInterface\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlanNumber\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Add a range of interfaces to a VLAN
     *
     * @param string $vlanNumber VLAN ID
     * @param string $interface Interface module (e.g., "Gi0/0")
     * @param string $interfaceRange Range of ports (e.g., "1 - 10")
     * @return string Command output
     */
    public function addInterfaceRangeToVlan(string $vlanNumber, string $interface, string $interfaceRange): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "interface range $interface/$interfaceRange\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlanNumber\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Configure an interface
     *
     * @param string $interface Interface name
     * @param string $type Interface type (e.g., "Gi", "Fa")
     * @param string $description Interface description
     * @param string $speed Interface speed
     * @param string $duplexMode Duplex mode (auto, full, half)
     * @return string Command output
     */
    public function configureInterface(
        string $interface,
        string $type,
        string $description,
        string $speed,
        string $duplexMode
    ): string {
        $cmd = "enable\nconfig t\n";
        $cmd .= "interface $type $interface\n";
        $cmd .= "description $description\n";
        $cmd .= "speed $speed\n";
        $cmd .= "duplex $duplexMode\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Set IP address on a VLAN interface
     *
     * @param string $vlanNumber VLAN ID
     * @param string $description Interface description
     * @param string $ipAddress IP address
     * @param string $netmask Network mask
     * @return string Command output
     */
    public function setVlanIpAddress(string $vlanNumber, string $description, string $ipAddress, string $netmask): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "int vlan $vlanNumber\n";
        $cmd .= "desc $description\n";
        $cmd .= "ip address $ipAddress $netmask\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Add a static route
     *
     * @param string $networkIp Destination network IP
     * @param string $netmask Network mask
     * @param string $gateway Gateway IP address
     * @param string $metric Route metric value
     * @return string Command output
     */
    public function addRoute(string $networkIp, string $netmask, string $gateway, string $metric): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "ip route $networkIp $netmask $gateway metric $metric\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Add default gateway
     *
     * @param string $gateway Gateway IP address
     * @return string Command output
     */
    public function addDefaultGateway(string $gateway): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "ip default gateway $gateway\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Add a basic ACL rule
     *
     * @param string $aclNumber ACL list number
     * @param string $action Action (permit or deny)
     * @param string $ip IP address
     * @param string $mask Wildcard mask
     * @return string Command output
     */
    public function addAclBasic(string $aclNumber, string $action, string $ip, string $mask): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "access-list $aclNumber $action $ip $mask\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Configure NetFlow for traffic monitoring
     *
     * @param string $collectorIp IP address of NetFlow collector
     * @param string $sourceInterface Interface to use as source
     * @return string Command output
     */
    public function configureNetflow(string $collectorIp, string $sourceInterface): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "ip flow-export destination $collectorIp 2055\n";
        $cmd .= "ip flow-export source $sourceInterface\n";
        $cmd .= "ip flow-export version 5\n";
        $cmd .= "ip flow-cache timeout active 1\n";
        $cmd .= "ip flow-cache timeout inactive 15\n";
        $cmd .= "snmp-server ifindex persist\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }

    /**
     * Enable NetFlow monitoring on an interface
     *
     * @param string $interface Interface to monitor
     * @return string Command output
     */
    public function addInterfaceToNetflowMonitoring(string $interface): string
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "interface $interface\n";
        $cmd .= "ip flow ingress\n";
        $cmd .= "copy running-config startup-config";
        return $this->remoteExec($cmd);
    }
}
