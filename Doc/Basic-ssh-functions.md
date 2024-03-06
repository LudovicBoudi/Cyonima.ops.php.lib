# Basic ssh functions

To use those functions:

```php
include (<path to lib>/ops.ssh.php);
```

## Execute a shell command on remote target

SshCmd($host, $login, $passwd, $command)

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $command  => Shell command to execute on target

## Upload a file on remote target

ScpPut($host, $login, $passwd, $src, $dest)

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file to put on remote target
* $dest     => local path and filemane of the file to upload on the remote target

## Download a file from a remote target

ScpGet($host, $login, $passwd, $src, $dest)

* $host     => remote target by name or IP address
* $login    => login account name
* $passwd   => Password of the account
* $src      => path and filename of the file on the remote target
* $dest     => local path and filemane of the file to download