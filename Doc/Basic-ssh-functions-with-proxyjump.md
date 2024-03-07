# Basic ssh functions with ProxyJump

To use those functions:

```php
include (<path to lib>/ops.ssh.proxyjump.php);
```

## Execute a shell command on remote target

ProxySshCmd($Proxy, $host, $login, $passwd, $command)

* $Proxy    => Proxy server
* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $command  => Shell command to execute on target

## Upload a file on remote target

ProxyScpPut($Proxy, $host, $login, $passwd, $src, $dest)

* $Proxy    => Proxy server
* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file to put on remote target
* $dest     => local path and filemane of the file to upload on the remote target

## Download a file from a remote target

ProxyScpGet($Proxy, $host, $login, $passwd, $src, $dest)

* $Proxy    => Proxy server
* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file on the remote target
* $dest     => local path and filemane of the file to download