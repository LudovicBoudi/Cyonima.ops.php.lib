## Arkoon firewalls

Specific port of scp command for Arkoon firewalls only, as those firewalls do not support interactive shell connection.

To use those functions:

```php
include (<path to lib>/ops.ssh.arkoon.php);
```


## Upload a file on remote target

ArkScpPut($host, $login, $passwd, $src, $dest)

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file to put on remote target
* $dest                             => local path and filemane of the file to upload on the remote target

## Download a file from a remote target

ArkScpGet($host, $login, $passwd, $src, $dest)

* $host                             => remote target by name or IP address
* $login                            => login account name
* $passwd                           => Password of the account
* $src                              => path and filename of the file on the remote target
* $dest                             => local path and filemane of the file to download