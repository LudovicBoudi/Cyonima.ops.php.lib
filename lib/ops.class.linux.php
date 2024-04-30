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
        $cmd = "echo $sudo_password | sudo -S systemctl $status $service";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $service, $status, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S systemctl $status $service";
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
        $cmd = "echo $sudo_password | sudo -S systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function systemctl_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $service, $status, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S systemctl $status $service";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function apt_install_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_install_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function apt_install_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_install_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function zypper_install_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_install_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function zypper_install_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_install_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function yum_install_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_install_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function yum_install_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_install_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function dnf_install_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_install_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf install -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function dnf_install_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_install_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf install -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function apt_uninstall_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_uninstall_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function apt_uninstall_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_uninstall_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function zypper_uninstall_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_uninstall_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function zypper_uninstall_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_uninstall_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function yum_uninstall_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_uninstall_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function yum_uninstall_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_uninstall_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function dnf_uninstall_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_uninstall_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_basic_auth($host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf remove -y $package";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function dnf_uninstall_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_uninstall_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $package, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf remove -y $package";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function apt_upgrade_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt update";
        $cmd .= "apt upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_upgrade_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt update";
        $cmd .= "apt upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt update";
        $cmd .= "sudo apt upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt update";
        $cmd .= "sudo apt upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt update; apt upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt update; apt upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function apt_upgrade_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt update";
        $cmd .= "apt upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_upgrade_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "apt update";
        $cmd .= "apt upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt update";
        $cmd .= "sudo apt upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo apt update";
        $cmd .= "sudo apt upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt update; apt upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function apt_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S apt update; apt upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function zypper_upgrade_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper refresh";
        $cmd .= "zypper update -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_upgrade_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper refresh";
        $cmd .= "zypper update -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper refresh";
        $cmd .= "sudo zypper update -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper refresh";
        $cmd .= "sudo zypper update -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper refresh; zypper update -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper refresh; zypper update -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function zypper_upgrade_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper refresh";
        $cmd .= "zypper update -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_upgrade_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "zypper refresh";
        $cmd .= "zypper update -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper refresh";
        $cmd .= "sudo zypper update -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo zypper refresh";
        $cmd .= "sudo zypper update -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper refresh; zypper update -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function zypper_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S zypper refresh; zypper update -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function yum_upgrade_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_upgrade_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum upgrade -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function yum_upgrade_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_upgrade_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "yum upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo yum upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function yum_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S yum upgrade -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function dnf_upgrade_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_upgrade_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function dnf_upgrade_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_upgrade_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "sudo dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function dnf_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $cmd = "echo $sudo_password | sudo -S dnf upgrade --refresh -y";
        $output = $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // Basic ssh remote
    public function factor_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "lsb_release -a";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);        
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "lscpu";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);       
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "ip a";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);      
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "lspci";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);      
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "df -h";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function factor_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "lsb_release -a";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);       
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "lscpu";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);      
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "ip a";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);     
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "lspci";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);   
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "df -h";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function factor_with_priviledge_elevation_basic_auth($host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "sudo lsb_release -a";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);    
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "sudo lscpu";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);      
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "sudo ip a";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);      
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "sudo lspci";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);  
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "sudo df -h";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);
        return $output;
    }
    public function factor_with_priviledge_elevation_id_rsa($host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "sudo lsb_release -a";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);   
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "sudo lscpu";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);     
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "sudo ip a";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);     
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "sudo lspci";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);  
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "sudo df -h";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function factor_with_priviledge_elevation_basic_auth_with_sudo_password($host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lsb_release -a";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd); 
        $output .="\n\n\t Section 2-> CPU architecture\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lscpu"; 
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);     
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "echo $sudo_password | sudo -S ip a"; 
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd);    
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "echo $sudo_password | sudo -S lspci"; 
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd); 
        $output .="\n\n\t Section 5-> Storage\n\n";   
        $cmd = "echo $sudo_password | sudo -S df -h";
        $output .= $ssh_con->ssh_command($host, $login, $password,$cmd); 
        return $output;
    }
    public function factor_with_priviledge_elevation_id_rsa_with_sudo_password($host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lsb_release -a";
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        $output .="\n\n\t Section 2-> CPU architecture\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lscpu"; 
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);     
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "echo $sudo_password | sudo -S ip a"; 
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);    
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "echo $sudo_password | sudo -S lspci"; 
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        $output .="\n\n\t Section 5-> Storage\n\n";   
        $cmd = "echo $sudo_password | sudo -S df -h"; 
        $output .= $ssh_con->ssh_command($host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    // ProxyJump ssh remote
    public function factor_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "lsb_release -a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);       
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "lscpu";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);     
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "ip a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);      
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "lspci";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);      
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "df -h"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd); 
        return $output;
    }
    public function factor_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "lsb_release -a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);      
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "lscpu";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);    
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "ip a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);     
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "lspci";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);     
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "df -h"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);   
        return $output;
    }
    public function factor_with_priviledge_elevation_basic_auth_with_proxyjump($proxy, $host, $login, $password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "sudo lsb_release -a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);  
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "sudo lscpu";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);      
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "sudo ip a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);     
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "sudo lspci"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd); 
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "sudo df -h";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function factor_with_priviledge_elevation_id_rsa_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "sudo lsb_release -a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        $output .="\n\n\t Section 2-> CPU architecture\n\n";  
        $cmd = "sudo lscpu";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);     
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "sudo ip a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);    
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "sudo lspci"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        $output .="\n\n\t Section 5-> Storage\n\n";  
        $cmd = "sudo df -h";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        return $output;
    }
    public function factor_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump($proxy, $host, $login, $password, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lsb_release -a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        $output .="\n\n\t Section 2-> CPU architecture\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lscpu"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);    
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "echo $sudo_password | sudo -S ip a"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);   
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "echo $sudo_password | sudo -S lspci"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        $output .="\n\n\t Section 5-> Storage\n\n";   
        $cmd = "echo $sudo_password | sudo -S df -h"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $password,$cmd);
        return $output;
    }
    public function factor_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa, $sudo_password)
    {

        $ssh_con = new Ops_class_ssh();
        $output ="*****************************************************************************************************\n";
        $output .="****                                   Factor report                                             ****\n";
        $output .="*****************************************************************************************************\n";
        $output .="\n\t Section 1-> system version\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lsb_release -a";
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);
        $output .="\n\n\t Section 2-> CPU architecture\n\n"; 
        $cmd = "echo $sudo_password | sudo -S lscpu"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);   
        $output .="\n\n\t Section 3-> Network\n\n";  
        $cmd = "echo $sudo_password | sudo -S ip a"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd);   
        $output .="\n\n\t Section 4-> PCI devices\n\n";  
        $cmd = "echo $sudo_password | sudo -S lspci"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd); 
        $output .="\n\n\t Section 5-> Storage\n\n";   
        $cmd = "echo $sudo_password | sudo -S df -h"; 
        $output .= $ssh_con->ssh_command_with_proxyjump($proxy, $host, $login, $id_rsa_pub, $id_rsa,$cmd); 
        return $output;
    }
    public function __call($method, $arguments)
    {
        if($method == 'systemctl')
        {
            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'systemctl_basic_auth'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'systemctl_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'systemctl_with_priviledge_elevation')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'systemctl_with_priviledge_elevation_with_sudo_password')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'systemctl_with_proxyjump')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'systemctl_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'systemctl_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'systemctl_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'systemctl_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if(count($arguments) == 7)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 8)
            {
                return call_user_func_array(array($this,'systemctl_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'apt_install')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_install_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_install_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_install_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_id_rsa'), $arguments);
            }
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_install_with_priviledge_elevation_with_sudo_password')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_install_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_install_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_install_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_install_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_install_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'apt_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'zypper_install')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_install_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_install_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_install_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_install_with_priviledge_elevation_with_sudo_password')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_install_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_install_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_install_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_install_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_install_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if(count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'zypper_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'yum_install')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_install_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_install_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_install_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_install_with_priviledge_elevation_with_sudo_password')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_install_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_install_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_install_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_install_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_install_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'yum_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'dnf_install')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_install_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_install_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_install_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_install_with_priviledge_elevation_rsa_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_install_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_install_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_install_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_install_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_install_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'dnf_install_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'apt_uninstall')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_uninstall_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_uninstall_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_uninstall_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_uninstall_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_uninstall_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_uninstall_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_uninstall_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_uninstall_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'apt_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'zypper_uninstall')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_uninstall_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_uninstall_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_uninstall_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_uninstall_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_uninstall_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_uninstall_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_uninstall_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_uninstall_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'zypper_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'yum_uninstall')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_uninstall_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_uninstall_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_uninstall_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_uninstall_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_uninstall_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_uninstall_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_uninstall_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_uninstall_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'yum_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'dnf_uninstall')
        {
            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_uninstall_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_uninstall_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_uninstall_with_priviledge_elevation')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_uninstall_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_uninstall_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_uninstall_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_uninstall_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_uninstall_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 7)
            {
                return call_user_func_array(array($this,'dnf_uninstall_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'apt_upgrade')
        {
            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'apt_upgrade_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_upgrade_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_upgrade_with_priviledge_elevation')
        {

            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_upgrade_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_upgrade_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_upgrade_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_upgrade_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_upgrade_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'apt_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'apt_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'zypper_upgrade')
        {
            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'zypper_upgrade_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_upgrade_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_upgrade_with_priviledge_elevation')
        {

            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_upgrade_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_upgrade_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_upgrade_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_upgrade_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_upgrade_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'zypper_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'zypper_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'yum_upgrade')
        {
            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'yum_upgrade_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_upgrade_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_upgrade_with_priviledge_elevation')
        {

            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_upgrade_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_upgrade_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_upgrade_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_upgrade_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_upgrade_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'yum_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'yum_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'dnf_upgrade')
        {
            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'dnf_upgrade_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_upgrade_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_upgrade_with_priviledge_elevation')
        {

            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_upgrade_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_upgrade_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_upgrade_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_upgrade_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_upgrade_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'dnf_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'dnf_upgrade_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if($method == 'factor')
        {
            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'factor_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'factor_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'factor_with_priviledge_elevation')
        {

            if(count($arguments) == 3)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_basic_auth'), $arguments);
            }
            else if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_id_rsa'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'factor_with_priviledge_elevation_with_sudo_password')
        {

            if (count($arguments) == 4)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_basic_auth_with_sudo_password'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_id_rsa_with_sudo_password'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'factor_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'factor_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'factor_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'factor_with_priviledge_elevation_with_proxyjump')
        {

            if(count($arguments) == 4)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_basic_auth_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_id_rsa_with_proxyjump'), $arguments);
            }
            else
            {
                return "Too many or too few arguments";
            }
        }
        else if ($method == 'factor_with_priviledge_elevation_with_proxyjump_with_sudo_password')
        {

            if (count($arguments) == 5)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_basic_auth_with_sudo_password_with_proxyjump'), $arguments);
            }
            else if (count($arguments) == 6)
            {
                return call_user_func_array(array($this,'factor_with_priviledge_elevation_id_rsa_with_sudo_password_with_proxyjump'), $arguments);
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