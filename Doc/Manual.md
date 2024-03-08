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
* $Search  => String to be replace in file
* $Replace => String that replace original one
* $file    => File that need to be modify 

## Remote functions

### Arkoon firewalls

Specific port of scp command for Arkoon firewalls only, as those firewalls do not support interactive shell connection.

To use those functions:

```php
include (<path to lib>/ops.ssh.arkoon.php);
```


#### Upload a file on remote target

ArkScpPut($host, $login, $passwd, $src, $dest)

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file to put on remote target
* $dest     => local path and filemane of the file to upload on the remote target

#### Download a file from a remote target

ArkScpGet($host, $login, $passwd, $src, $dest)

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file on the remote target
* $dest     => local path and filemane of the file to download

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

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $command  => Shell command to execute on target

or 

* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $command      => Shell command to execute on target

#### scp_put arguments

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file to put on remote target
* $dest     => local path and filemane of the file to upload on the remote target

or

* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file to put on remote target
* $dest         => local path and filemane of the file to upload on the remote target

#### scp_get arguments

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file on the remote target
* $dest     => local path and filemane of the file to download

or

* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file on the remote target
* $dest         => local path and filemane of the file to download

#### ssh_command_with_proxyjump arguments

* $Proxy    => Proxy server
* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $command  => Shell command to execute on target

or

* $Proxy        => Proxy server
* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $command      => Shell command to execute on target

#### scp_put_with_proxyjump arguments

* $Proxy    => Proxy server
* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file to put on remote target
* $dest     => local path and filemane of the file to upload on the remote target

or

* $Proxy        => Proxy server
* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file to put on remote target
* $dest         => local path and filemane of the file to upload on the remote target

#### scp_get_with_proxyjump arguments

* $Proxy    => Proxy server
* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file on the remote target
* $dest     => local path and filemane of the file to download

or

* $Proxy        => Proxy server
* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file on the remote target
* $dest         => local path and filemane of the file to download