# Basic ssh functions with id_rsa auth

To use those functions:

```php
include (<path to lib>/ops.ssh.id_rsa.php);
```

## Execute a shell command on remote target

IdRsaSshCmd($host, $login, $id_rsa_pub, $id_rsa, $command)

* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $command      => Shell command to execute on target

## Upload a file on remote target

IdRsaScpPut($host, $login, $id_rsa_pub, $id_rsa, $src, $dest)

* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file to put on remote target
* $dest         => local path and filemane of the file to upload on the remote target

## Download a file from a remote target

IdRsaScpGet($host, $login, $id_rsa_pub, $id_rsa, $src, $dest)

* $host         => remote target by name or IP address
* $login        => login account name
* $id_rsa_pub   => path to id_rsa.pub file
* $id_rsa       => path to id_rsa file
* $src          => path and filename of the file on the remote target
* $dest         => local path and filemane of the file to download