<?php

use Dba\Connection;
use php2-ssh2\ssh2_connect;
use php2-ssh2\ssh2_tunnel;
use php2-ssh2\ssh2_auth_password;
use php2-ssh2\ssh2_auth_pubkey_file;
use php2-ssh2\ssh2_exec;
use php2-ssh2\ssh2_scp_send;
use php2-ssh2\ssh2_scp_recv;
use php2-ssh2\ssh2_disconnect;

function SeekAndReplace($Search,$Replace,$fichier)
{
	$contenu = str_replace($Search, $Replace, file_get_contents($fichier)); 
	file_put_contents($fichier, $contenu); 
}

function ReadStdIn()
{
        $fr=fopen("php://stdin","r"); 
        $input = fgets($fr,128);  
        $input = rtrim($input); 
        fclose ($fr); 
        return $input;
}
class Cyonima_Ops {
    # base properties
    private bool $Proxy;
    private string $ProxyJump;
    private bool $RSA;
    private string $Id_RSA;
    private string $Id_RSA_pub;
    private int $Port;
    private $Connection;
    private string $host;
    private string $login;
    private string $password;
    private $tunnel;

    # base class methods
    public function __construct(){
        $this->Proxy = false;
        $this->ProxyJump = null;
        $this->RSA = false;
        $this->Id_RSA = null;
        $this->Port = 22;
        $this->Connection = null;
        $this->host = null;
        $this->login = null;
        $this->password = null;
        $this->tunnel = null;
	    $this->Id_RSA_pub = null;
    }
    public function set_proxy (string $IP){
        $this->Proxy = true;
        $this->ProxyJump = $IP;
    }
    public function unset_proxy (){
        $this->Proxy = false;
        $this->ProxyJump = null;
    }
    public function set_RSA(string $log, string $key, string $key_pub){
        $this->RSA = true;
        $this->Id_RSA = $key;
        $this->Id_RSA_pub = $key_pub;
        $this->login = $log;
    }
    public function unset_RSA(){
        $this->RSA = false;
        $this->Id_RSA = null;
    }
    public function modify_port(int $value){
        $this->Port = $value;
    }
    public function set_host(string $IP){
        $this->host = $IP;
    }
    public function set_credentials(string $log, string $pass){
        $this->login = $log;
        $this->password = $pass;
    }
    # connection methods
    public function open_connection(){
        if($this->RSA == false and $this->Proxy == false){
            if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
                if (!($this->Connection = ssh2_connect($this->host, $this->Port))) 
                    {
                        return "connection failed";
                    }
                else
                    {
                        if (!ssh2_auth_password($this->Connection, $this->login, $this->password))
                        {
                            return "authentication failed";
                        }
                        else
                        {
                            return "authetication succed";
                        }
                    } 
        }
        elseif($this->RSA == false and $this->Proxy == true){
            if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
                if (!($this->Connection = ssh2_connect($this->ProxyJump, $this->Port))) 
                    {
                        return "connection failed";
                    }
                else
                    {
                        if (!ssh2_auth_password($this->Connection, $this->login, $this->password))
                        {
                            return "authentication failed";
                        }
                        else
                        {
                            $this->tunnel = ssh2_tunnel($this->Connection, $this->host, $this->Port);
                            return "authetication succed";
                        }
                    } 
        }        
        elseif($this->RSA == true and $this->Proxy == true){
            if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
                if (!($this->Connection = ssh2_connect($this->ProxyJump, $this->Port))) 
                    {
                        return "connection failed";
                    }
                else
                    {
                        if (!ssh2_auth_pubkey_file($this->Connection, $this->login, $this->Id_RSA_pub, $this->Id_RSA))
                        {
                            return "authentication failed";
                        }
                        else
                        {
                            $this->tunnel = ssh2_tunnel($this->Connection, $this->host, $this->Port);
                            return "authetication succed";
                        }
                    } 
        }           
        elseif($this->RSA == true and $this->Proxy == false){
            if(!function_exists("ssh2_connect")) die ("function ssh2_connect does not exist, the lib php-ssh2 might be missing");
                if (!($this->Connection = ssh2_connect($this->host, $this->Port))) 
                    {
                        return "connection failed";
                    }
                else
                    {
                        if (!ssh2_auth_pubkey_file($this->Connection, $this->login, $this->Id_RSA_pub, $this->Id_RSA))
                        {
                            return "authentication failed";
                        }
                        else
                        {
                            return "authetication succed";
                        }
                    } 
        }     
    }
    public function close_connection(){
        ssh2_disconnect($this->Connection);
    }
    # remote command execution
    public function remote_exec(string $cmd){
        if ($this->Proxy == false){
            if(!($stream = ssh2_exec($this->Connection, $cmd)))
                    {
                        return "shell command line failed";
                    }
                    else
                    {
                        stream_set_blocking($stream, true);
                        $data ="";
                        while ($buf = fread($stream, 4096))
                        {
                            $data .= $buf;						
                        }
                    fclose($stream);
                    return $data;            
                    }
        }
        elseif ($this->Proxy == true) {
            if(!($stream = ssh2_exec($this->tunnel, $cmd)))
                    {
                        return "shell command line failed";
                    }
                    else
                    {
                        stream_set_blocking($stream, true);
                        $data ="";
                        while ($buf = fread($stream, 4096))
                        {
                            $data .= $buf;						
                        }
                    fclose($stream);
                    return $data;            
                    }
        }
    }
    public function scp_put(string $src, string $dest, string $file_permission){
        if ($this->Proxy == false){
            ssh2_scp_send($this->Connection, $src, $dest, $file_permission);
        }
        elseif ($this->Proxy == true) {
            ssh2_scp_send($this->tunnel, $src, $dest, $file_permission);
        }
    }
    public function scp_get(string $src, string $dest){
        if ($this->Proxy == false){
            ssh2_scp_recv($this->Connection, $src, $dest);
        }
        elseif ($this->Proxy == true) {
            ssh2_scp_recv($this->tunnel, $src, $dest);
        }
    }
    #############################################################################################################################
    #                                     Network equipement                                                                    #
    #############################################################################################################################
    # cisco functions

    public function cisco_host_base_conf(string $hostname, string $location, string $time){
        $cmd = "enable\nconfig t\n";
        $cmd .= "hostname $hostname\n";
        $cmd .= "prompt $hostname>\n";
        $cmd .= "banner motd c $location c\n";
        $cmd .= "clock set $time\n";
        $output = $this->remote_exec($cmd);
        return $output;
    }

    public function cisco_add_user (string $username, string $userpassword)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "username $username create\n";
        $cmd .= "username $username password $userpassword\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function cisco_configure_vlan(string $vlan_number, string $vlan_name, string $vlan_interface)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "vlan $vlan_number\n";
        $cmd .= "name $vlan_name\n";
        $cmd .= "exit\n";
        $cmd .= "int $vlan_interface\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlan_number\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }

    public function cisco_add_interface_range_to_vlan(string $vlan_number, string $interface, string $interface_range)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "interface range $interface/$interface_range\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlan_number\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }

    public function cisco_configure_interface(string $interface, string $interface_type, string $interface_description, string $interface_speed, string $interface_mode)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "interface $interface_type $interface\n";
        $cmd .= "description $interface_description\n";
        $cmd .= "speed $interface_speed\n";
        $cmd .= "duplex $interface_mode\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function cisco_set_vlan_ip_address(string $vlan_number, string $vlan_description, string $vlan_ipaddress, string $vlan_mask)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "int vlan $vlan_number\n";
        $cmd .= "desc $vlan_description\n";
        $cmd .= "ip address $vlan_ipaddress $vlan_mask\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function cisco_add_route(string $network_ip, string $mask, string $ip_address, string $metric_value)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "ip route $network_ip $mask $ip_address metric $metric_value\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function cisco_add_default_gateway(string $gateway)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "ip default gateway $gateway\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function cisco_add_acl_basic(string $acl_list_number, string $action, string $IP, string $mask)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "access-list $acl_list_number $action $IP $mask\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }

    public function cisco_configure_netflow(string $netflow_collector_IP, string $interface)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "ip flow-export destination $netflow_collector_IP 2055\n";
        $cmd .= "ip flow-export source $interface\n";
        $cmd .= "ip flow-export version 5\n";
        $cmd .= "ip flow-cache timeout active 1\n";
        $cmd .= "ip flow-cache timeout inactive 15\n";
        $cmd .= "snmp-server ifindex persist\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function cisco_add_interface_to_netflow_monitoring(string $interface)
    {
        $cmd = "enable\nconfig t\n";
        $cmd .= "interface $interface\n";
        $cmd .= "ip flow ingress\n";
        $cmd .= "copy running-config startup-config";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    #############################################################################################################################
    #                                       Linux equipement                                                                    #
    #############################################################################################################################


    public function linux_systemctl(string $service, string $status)
    {
        $cmd = "systemctl $status $service";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_systemctl_with_priviledge_elevation(string $service, string $status, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S systemctl $status $service";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_systemctl_with_priviledge_elevation_no_password(string $service, string $status)
    {
        $cmd = "sudo systemctl $status $service";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_install(string $package)
    {
        $cmd = "apt install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_install_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S apt install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_install_with_priviledge_elevation_no_password(string $package)
    {
        $cmd = "sudo apt install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_install(string $package)
    {
        $cmd = "zypper install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_install_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S zypper install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_install_with_priviledge_elevation_no_password($host, $login, $password, $package)
    {
        $cmd = "sudo zypper install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_install(string $package)
    {
        $cmd = "yum install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_install_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S yum install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_install_with_priviledge_elevation_no_password(string $package)
    {
        $cmd = "sudo yum install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_install(string $package)
    {
        $cmd = "dnf install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_install_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S dnf install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_install_with_priviledge_elevation_no_password(string $package)
    {
        $cmd = "sudo dnf install -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_uninstall(string $package)
    {
        $cmd = "apt remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_uninstall_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S apt remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_uninstall_with_priviledge_elevation_no_password(string $package)
    {
        $cmd = "sudo apt remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_uninstall(string $package)
    {
        $cmd = "zypper remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_uninstall_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S zypper remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_uninstall_with_priviledge_elevation_no_password(string $package)
    {
        $cmd = "sudo zypper remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_uninstall(string $package)
    {
        $cmd = "yum remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_uninstall_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S yum remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_uninstall_with_priviledge_elevation_no_password(string $package)
    {
        $cmd = "sudo yum remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_uninstall(string $package)
    {
        $cmd = "dnf remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_uninstall_with_priviledge_elevation(string $package, string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S dnf remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_uninstall_with_priviledge_elevation_no_password(string $package)
    {
        $cmd = "sudo dnf remove -y $package";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_upgrade()
    {
        $cmd = "apt update";
        $cmd .= "apt upgrade -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_upgrade_with_priviledge_elevation(string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S apt update; apt upgrade -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_apt_upgrade_with_priviledge_elevation_no_password()
    {;
        $cmd = "sudo apt update";
        $cmd .= "sudo apt upgrade -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_upgrade()
    {
        $cmd = "zypper refresh";
        $cmd .= "zypper update -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_upgrade_with_priviledge_elevation(string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S zypper refresh; zypper update -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_zypper_upgrade_with_priviledge_elevation_no_password()
    {
        $cmd = "sudo zypper refresh";
        $cmd .= "sudo zypper update -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_upgrade()
    {
        $cmd = "yum upgrade -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_upgrade_with_priviledge_elevation(string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S yum upgrade -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_yum_upgrade_with_priviledge_elevation_no_password()
    {
        $cmd = "sudo yum upgrade -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_upgrade()
    {
        $cmd = "dnf upgrade --refresh -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_upgrade_with_priviledge_elevation(string $sudo_password)
    {
        $cmd = "echo $sudo_password | sudo -S dnf upgrade --refresh -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_dnf_upgrade_with_priviledge_elevation_no_password()
    {
        $cmd = "sudo dnf upgrade --refresh -y";
        $output = $this->remote_exec($cmd);
        return $output;
    }
    public function linux_add_user(string $username,string $group, string $password) 
    {
        $cmd = "adduser -m -s /bin/bash $group $username\n";
        $cmd .= "echo $password | sudo  passwd $username\n";
        $output = $this->remote_exec($cmd);
        return $output;
	}
    public function linux_add_user_with_priviledge_elevation(string $username, string $password, string $sudo_password)
    {
    $cmd = "echo $sudo_password | sudo -S useradd -m -s /bin/bash $username && echo '$username:$password' | sudo chpasswd";
    $output = $this->remote_exec($cmd);
    return $output;
    }

    public function linux_delete_user(string $username)
    {
    $cmd = "sudo userdel -r $username";
    $output = $this->remote_exec($cmd);
    return $output;
    }
    public function linux_delete_user_with_priviledge_elevation(string $username, string $sudo_password)
    {
    $cmd = "echo $sudo_password | sudo -S userdel -r $username";
    $output = $this->remote_exec($cmd);
    return $output;
    }
    public function linux_change_password_with_priviledge_elevation(string $username, string $new_password, string $sudo_password)
    {
    $cmd = "echo $sudo_password | sudo -S bash -c \"echo '$username:$new_password' | chpasswd\"";
    $output = $this->remote_exec($cmd);
    return $output;
    }
    public function linux_change_password(string $username, string $new_password)
    {
    $cmd = "echo '$username:$new_password' | chpasswd";
    $output = $this->remote_exec($cmd);
    return $output;
    }





}


?>
