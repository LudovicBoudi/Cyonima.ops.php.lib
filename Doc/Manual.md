# Ops PHP Lib by Cyonima

Description : Set of PHP class and lib for OPS and Sysadmin for PHP scripting. Most of PHP class and functions you'll find here are designed for remote operations, but you'll find two local libs for none remote little actions.

## Local functions

### Local stdin

To use those functions:

```php
include (<path to lib>/ops.local.stdin.php);
```

#### Read local stdin:

Usage:

```php
$output = ReadStdIn();
```

### Local SeekAndReplace

To use those functions:

```php
include (<path to lib>/ops.local.SeeckAndReplace.php);
```

#### Usage

SeekAndReplace($Search,$Replace,$file)
* $Search                           => String to be replace in file
* $Replace                          => String that replace original one
* $file                             => File that need to be modify 

## Remote functions

### Arkoon firewalls

Specific port of scp command for Arkoon firewalls only, as those firewalls do not support interactive shell connection.

To use those functions:

```php
include (<path to lib>/ops.ssh.arkoon.php);
```


#### Upload a file on remote target

ArkScpPut($host, $login, $passwd, $src, $dest)

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file to put on remote target
* $dest                             => local path and filemane of the file to upload on the remote target

#### Download a file from a remote target

ArkScpGet($host, $login, $passwd, $src, $dest)

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file on the remote target
* $dest                             => local path and filemane of the file to download

### Ops SSH Class

To use this Class:

```php
include (<path to lib>/ops.class.ssh.php);

$var = new Ops_class_ssh();
```

This Class brings you 6 methods :
* ssh_command
* ssh_command_with_proxyjump
* scp_put
* scp_put_with_proxyjump
* scp_get
* scp_get_with_proxyjump

#### ssh_command arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $command                          => Shell command to execute on target

or 

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $command                          => Shell command to execute on target

#### scp_put arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file to put on remote target
* $dest                             => local path and filemane of the file to upload on the remote target

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $src                              => path and filename of the file to put on remote target
* $dest                             => local path and filemane of the file to upload on the remote target

#### scp_get arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file on the remote target
* $dest                             => local path and filemane of the file to download

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $src                              => path and filename of the file on the remote target
* $dest                             => local path and filemane of the file to download

#### ssh_command_with_proxyjump arguments

* $Proxy                            => Proxy server
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $command                          => Shell command to execute on target

or

* $Proxy                            => Proxy server
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $command                          => Shell command to execute on target

#### scp_put_with_proxyjump arguments

* $Proxy                            => Proxy server
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file to put on remote target
* $dest                             => local path and filemane of the file to upload on the remote target

or

* $Proxy                            => Proxy server
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $src                              => path and filename of the file to put on remote target
* $dest                             => local path and filemane of the file to upload on the remote target

#### scp_get_with_proxyjump arguments

* $Proxy                            => Proxy server
* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file on the remote target
* $dest                             => local path and filemane of the file to download

or

* $Proxy                            => Proxy server
* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $src                              => path and filename of the file on the remote target
* $dest                             => local path and filemane of the file to download

### Ops Cisco Switch Class

To use this Class:

```php
include (<path to lib>/ops.class.ssh.php);
include (<path to lib>/ops.class.cisco.switch.php);

$var = new Ops_class_cisco_switch();
```

This class brings 10 methods :
* add_user
* configure_vlan
* add_interface_range_to_vlan
* configure_interface
* set_vlan_ip_address
* add_route
* add_default_gateway
* add_acl
* configure_netflow
* add_interface_to_netflow_monitoring

#### add_user arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $username                         => the logon of your new user
* $userpassword                     => the password for your new account

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $username                         => the logon of your new user
* $userpassword                     => the password for your new account

#### configure_vlan arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $vlan_number                      => your vlan number
* $vlan_name                        => your vlan name
* $vlan_interface                   => your vlan interface

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $vlan_number                      => your vlan number
* $vlan_name                        => your vlan name
* $vlan_interface                   => your vlan interface

#### add_interface_range_to_vlan arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $vlan_number                      => your vlan number
* $interface                        => the network interface
* $interface_range                  => the interface range

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $vlan_number                      => your vlan number
* $interface                        => the network interface
* $interface_range                  => the interface range

#### configure_interface arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $interface                        => your network interface
* $interface_type                   => interface type
* $interface_description            => interface description
* $interface_speed                  => interface speed
* $interface_mode                   => interface mode

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $interface                        => your network interface
* $interface_type                   => interface type
* $interface_description            => interface description
* $interface_speed                  => interface speed
* $interface_mode                   => interface mode

#### set_vlan_ip_address arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $vlan_number                      => the vlan number
* $vlan_description                 => the vlan description
* $vlan_ipaddress                   => the vlan IP address
* $vlan_mask                        => the vlan mask

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $vlan_number                      => the vlan number
* $vlan_description                 => the vlan description
* $vlan_ipaddress                   => the vlan IP address
* $vlan_mask                        => the vlan mask


#### add_route arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $network_ip                       => you networl IP
* $mask                             => the IP mask
* $ip_address                       => the IP address
* $metric_value                     => the metric value

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $network_ip                       => you networl IP
* $mask                             => the IP mask
* $ip_address                       => the IP address
* $metric_value                     => the metric value

#### add_default_gateway arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $gateway                          => Gateway to add

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $gateway                          => Gateway to add


#### add_acl arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $acl_list_number                  => number of the ACL list
* $action                           => two options "deny" or "permit"
* $IP                               => IP targeted by the ACL
* $mask                             => mask

or 

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $acl_list_number                  => number of the ACL list
* $action                           => two options "deny" or "permit"
* $IP                               => IP targeted by the ACL
* $mask                             => mask

#### configure_netflow arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $netflow_collector_IP             => your network NetFlow collector IP
* $netflow_collector_interface      => your network NetFlow collector interface

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $netflow_collector_IP             => your network NetFlow collector IP
* $netflow_collector_interface      => your network NetFlow collector interface

#### add_interface_to_netflow_monitoring arguments

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $interface                        => interface to add to NetFlow monitoring

or

* $host                             => remote target by name or IP address
* $login                            => login account name
* $id_rsa_pub                       => path to id_rsa.pub file
* $id_rsa                           => path to id_rsa file
* $interface                        => interface to add to NetFlow monitoring


