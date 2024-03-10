<?php
class Ops_class_cisco_switch
{
    public function add_user_basic_auth ($host, $login, $password, $username, $userpassword)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "username $username create\n";
        $cmd .= "username $username password $userpassword\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function add_user_id_rsa ($host, $login, $id_rsa_pub, $id_rsa, $username, $userpassword)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "username $username create\n";
        $cmd .= "username $username password $userpassword\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function configure_vlan_basic_auth($host, $login, $password, $vlan_number, $vlan_name, $vlan_interface)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "vlan $vlan_number\n";
        $cmd .= "name $vlan_name\n";
        $cmd .= "exit\n";
        $cmd .= "int $vlan_interface\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlan_number\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }

    public function configure_vlan_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $vlan_number, $vlan_name, $vlan_interface)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "vlan $vlan_number\n";
        $cmd .= "name $vlan_name\n";
        $cmd .= "exit\n";
        $cmd .= "int $vlan_interface\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlan_number\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
}
?>