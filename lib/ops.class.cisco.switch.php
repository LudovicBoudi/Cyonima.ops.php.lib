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

    public function add_interface_range_to_vlan_basic_auth($host, $login, $password, $vlan_number, $interface, $interface_range)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "interface range $interface/$interface_range\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlan_number\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }

    public function add_interface_range_to_vlan_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $vlan_number, $interface, $interface_range)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "interface range $interface/$interface_range\n";
        $cmd .= "switchport mode access\n";
        $cmd .= "switchport access vlan $vlan_number\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }

    public function configure_interface_basic_auth($host, $login, $password, $interface, $interface_type, $interface_description, $interface_speed, $interface_mode)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "interface $interface_type $interface\n";
        $cmd .= "description $interface_description\n";
        $cmd .= "speed $interface_speed\n";
        $cmd .= "duplex $interface_mode\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }

    public function configure_interface_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $interface, $interface_type, $interface_description, $interface_speed, $interface_mode)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "interface $interface_type $interface\n";
        $cmd .= "description $interface_description\n";
        $cmd .= "speed $interface_speed\n";
        $cmd .= "duplex $interface_mode\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }

    public function set_vlan_ip_address_basic_auth($host, $login, $password, $vlan_number, $vlan_description, $vlan_ipaddress, $vlan_mask)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "int vlan $vlan_number\n";
        $cmd .= "desc $vlan_description\n";
        $cmd .= "ip address $vlan_ipaddress $vlan_mask\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }

    public function set_vlan_ip_address_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $vlan_number, $vlan_description, $vlan_ipaddress, $vlan_mask)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "int vlan $vlan_number\n";
        $cmd .= "desc $vlan_description\n";
        $cmd .= "ip address $vlan_ipaddress $vlan_mask\n";
        $cmd .= "end\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function add_route_basic_auth($host, $login, $password, $network_ip, $mask, $ip_address, $metric_value)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "ip route $network_ip $mask $ip_address metric $metric_value\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }

    public function add_route_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $network_ip, $mask, $ip_address, $metric_value)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "ip route $network_ip $mask $ip_address metric $metric_value\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function add_default_gateway_basic_auth ($host, $login, $password, $gateway)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "ip default gateway $gateway\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function add_default_gateway_id_rsa ($host, $login, $id_rsa_pub, $id_rsa, $gateway)
    {
        $ssh_con = new Ops_class_ssh();
        $cmd = "config t\n";
        $cmd .= "ip default gateway $gateway\n";
        $cmd .= "copy running-config startup-config";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function __call($method, $arguments)
    {
        if($method == 'add_user')
        {
            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'add_user_basic_auth'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'add_user_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'configure_vlan')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'configure_vlan_basic_auth'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'configure_vlan_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'add_interface_range_to_vlan')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'add_interface_range_to_vlan_basic_auth'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'add_interface_range_to_vlan_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'configure_interface')
        {

            if(count($arguments) == 8)
            {
                return call_user_func_array(array($this,'configure_interface_basic_auth'), $arguments);
            }
            else if (count($arguments) == 9)
            {
                return call_user_func_array(array($this,'configure_interface_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'set_vlan_ip_address')
        {

            if(count($arguments) == 7)
            {
                return call_user_func_array(array($this,'set_vlan_ip_address_basic_auth'), $arguments);
            }
            else if (count($arguments) == 8)
            {
                return call_user_func_array(array($this,'set_vlan_ip_address_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'add_route')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'add_route_basic_auth'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'add_route_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'add_default_gateway')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'add_default_gateway_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'add_default_gateway_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else 
        {
            return "Unknow method";
        }
    }

}
?>