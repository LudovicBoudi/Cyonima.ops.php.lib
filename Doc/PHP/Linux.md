## Ops Linux Class

To use this Class:

```php
include (<path to lib>/ops.class.ssh.php);
include (<path to lib>/ops.class.linux.php);

$var = new Ops_class_linuxch();
```

The class methods:
* systemctl
* systemctl_with_priviledge_elevation
* systemctl_with_priviledge_elevation_with_sudo_password
* systemctl_with_proxyjump
* systemctl_with_priviledge_elevation_with_proxyjump
* systemctl_with_priviledge_elevation_with_proxyjump_with_sudo_password
* apt_install
* apt_install_with_priviledge_elevation
* apt_install_with_priviledge_elevation_with_sudo_password
* apt_install_with_proxyjump
* apt_install_with_priviledge_elevation_with_proxyjump
* apt_install_with_priviledge_elevation_with_proxyjump_with_sudo_password
* zypper_install
* zypper_install_with_priviledge_elevation
* zypper_install_with_priviledge_elevation_with_sudo_password
* zypper_install_with_proxyjump
* zypper_install_with_priviledge_elevation_with_proxyjump
* zypper_install_with_priviledge_elevation_with_proxyjump_with_sudo_password
* yum_install
* yum_install_with_priviledge_elevation
* yum_install_with_priviledge_elevation_with_sudo_password
* yum_install_with_proxyjump
* yum_install_with_priviledge_elevation_with_proxyjump
* yum_install_with_priviledge_elevation_with_proxyjump_with_sudo_password
* dnf_install
* dnf_install_with_priviledge_elevation
* dnf_install_with_priviledge_elevation_rsa_with_sudo_password
* dnf_install_with_proxyjump
* dnf_install_with_priviledge_elevation_with_proxyjump
* dnf_install_with_priviledge_elevation_with_proxyjump_with_sudo_password
* apt_uninstall
* apt_uninstall_with_priviledge_elevation
* apt_uninstall_with_priviledge_elevation_with_sudo_password
* apt_uninstall_with_proxyjump
* apt_uninstall_with_priviledge_elevation_with_proxyjump
* apt_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password
* zypper_uninstall
* zypper_uninstall_with_priviledge_elevation
* zypper_uninstall_with_priviledge_elevation_with_sudo_password
* zypper_uninstall_with_proxyjump
* zypper_uninstall_with_priviledge_elevation_with_proxyjump
* zypper_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password
* yum_uninstall
* yum_uninstall_with_priviledge_elevation
* yum_uninstall_with_priviledge_elevation_with_sudo_password
* yum_uninstall_with_proxyjump
* yum_uninstall_with_priviledge_elevation_with_proxyjump
* yum_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password
* dnf_uninstall
* dnf_uninstall_with_priviledge_elevation
* dnf_uninstall_with_priviledge_elevation_with_sudo_password
* dnf_uninstall_with_proxyjump
* dnf_uninstall_with_priviledge_elevation_with_proxyjump
* dnf_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password
* apt_upgrade
* apt_upgrade_with_priviledge_elevation
* apt_upgrade_with_priviledge_elevation_with_sudo_password
* apt_upgrade_with_proxyjump
* apt_upgrade_with_priviledge_elevation_with_proxyjump
* apt_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password
* zypper_upgrade
* zypper_upgrade_with_priviledge_elevation
* zypper_upgrade_with_priviledge_elevation_with_sudo_password
* zypper_upgrade_with_proxyjump
* zypper_upgrade_with_priviledge_elevation_with_proxyjump
* zypper_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password
* yum_upgrade
* yum_upgrade_with_priviledge_elevation
* yum_upgrade_with_priviledge_elevation_with_sudo_password
* yum_upgrade_with_proxyjump
* yum_upgrade_with_priviledge_elevation_with_proxyjump
* yum_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password
* dnf_upgrade
* dnf_upgrade_with_priviledge_elevation
* dnf_upgrade_with_priviledge_elevation_with_sudo_password
* dnf_upgrade_with_proxyjump
* dnf_upgrade_with_priviledge_elevation_with_proxyjump
* dnf_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password
* factor
* factor_with_priviledge_elevation
* factor_with_priviledge_elevation_with_sudo_password
* factor_with_proxyjump
* factor_with_priviledge_elevation_with_proxyjump
* factor_with_priviledge_elevation_with_proxyjump_with_sudo_password

 
## systemctl arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)

## systemctl_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)


## systemctl_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)
* $sudo_password                    => like the name describe


## systemctl_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)


## systemctl_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)



## systemctl_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $service                          => systemctl service
* $status                           => action on the service (start, stop, restart, status and for certain services reload)
* $sudo_password                    => like the name describe


## apt_install arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_install_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_install_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## apt_install_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_install_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_install_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## zypper_install arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_install_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_install_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## zypper_install_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_install_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_install_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## yum_install arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_install_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_install_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## yum_install_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_install_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_install_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## dnf_install arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_install_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_install_with_priviledge_elevation_rsa_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## dnf_install_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_install_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_install_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## apt_uninstall arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_uninstall_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_uninstall_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## apt_uninstall_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_uninstall_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## apt_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## zypper_uninstall arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_uninstall_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_uninstall_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## zypper_uninstall_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_uninstall_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## zypper_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## yum_uninstall arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_uninstall_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_uninstall_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## yum_uninstall_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_uninstall_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## yum_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## dnf_uninstall arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_uninstall_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_uninstall_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## dnf_uninstall_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_uninstall_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 


## dnf_uninstall_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $Package                          => package name 
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $Package                          => package name 
* $sudo_password                    => like the name describe


## apt_upgrade arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## apt_upgrade_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## apt_upgrade_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## apt_upgrade_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## apt_upgrade_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## apt_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## zypper_upgrade arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## zypper_upgrade_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## zypper_upgrade_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## zypper_upgrade_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## zypper_upgrade_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## zypper_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## yum_upgrade arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## yum_upgrade_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## yum_upgrade_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## yum_upgrade_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## yum_upgrade_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## yum_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## dnf_upgrade arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## dnf_upgrade_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## dnf_upgrade_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## dnf_upgrade_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## dnf_upgrade_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## dnf_upgrade_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## factor arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## factor_with_priviledge_elevation arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## factor_with_priviledge_elevation_with_sudo_password arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe


## factor_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## factor_with_priviledge_elevation_with_proxyjump arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file


## factor_with_priviledge_elevation_with_proxyjump_with_sudo_password arguments

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $sudo_password                    => like the name describe

or

* $Proxy                            => Proxyjump host (bastion for example)
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $sudo_password                    => like the name describe
