<?php

class Ops_class_linux
{
    // Basic ssh remote
    public function systemctl_basic_auth($host, $login, $password, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "systemctl $status $service";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "systemctl $status $service";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_basic_auth($host, $login, $password, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo systemctl $status $service";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo systemctl $status $service";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $service, $status, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo systemctl $status $service";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $service, $status, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo systemctl $status $service";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function systemctl_basic_auth_with_proxyjump($proxy, $host, $login, $password, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $service, $status)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $service, $status, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $service, $status, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
}

?>