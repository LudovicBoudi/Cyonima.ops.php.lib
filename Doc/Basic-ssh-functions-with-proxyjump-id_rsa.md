# Basic ssh functions with ProxyJump and id_rsa auth

To use those functions:

```php
include (<path to lib>/ops.ssh.proxyjump.id_rsa.php);
```

## Execute a shell command on remote target

IdRsaProxySshCmd($Proxy, $host, $login, $id_rsa_pub, $id_rsa, $command)

* $Proxy        => Proxy server
* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $command      => Shell command to execute on target

## Upload a file on remote target

IdRsaProxyScpPut($Proxy, $host, $login, $id_rsa_pub, $id_rsa, $src, $dest)

* $Proxy        => Proxy server
* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file to put on remote target
* $dest         => local path and filemane of the file to upload on the remote target

## Download a file from a remote target

IdRsaProxyScpGet($Proxy, $host, $login, $id_rsa_pub, $id_rsa, $src, $dest)

* $Proxy        => Proxy server
* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file on the remote target
* $dest         => local path and filemane of the file to download