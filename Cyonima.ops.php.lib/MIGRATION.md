# Migration Guide: v1.0.0 to v2.0.0

This document outlines the breaking changes and migration path for upgrading from version 1.0.0 to version 2.0.0 of the Cyonima OPS PHP Library.

## Overview of Changes

Version 2.0.0 is a complete rewrite that modernizes the codebase to follow current PHP best practices:

- **PSR-4 Namespaces**: All classes are now properly namespaced
- **Exception Handling**: Replaced string error returns with proper exceptions
- **Type Safety**: Added comprehensive type hints and return types
- **Fluent Interface**: Configuration methods now support method chaining
- **Code Organization**: Classes are now split into logical modules
- **Documentation**: Full PHPDoc coverage for all public methods

## Class Name Changes

| v1.0.0 | v2.0.0 | Namespace |
|--------|--------|-----------|
| `Cyonima_Ops` | `AbstractOps` | `Cyonima\Ops` |
| `Ops_Linux` | `AbstractLinuxOps` | `Cyonima\Ops\Linux` |
| `Ops_Cisco` | `CiscoOps` | `Cyonima\Ops\Network` |
| `Ops_Suse` | `SuseOps` | `Cyonima\Ops\Linux` |
| `Ops_Ubuntu` | `UbuntuOps` | `Cyonima\Ops\Linux` |
| `Ops_Fedora` | `FedoraOps` | `Cyonima\Ops\Linux` |
| `Ops_RedHat` | `RedhatOps` | `Cyonima\Ops\Linux` |
| `Ops_Rocky` | `RockyOps` | `Cyonima\Ops\Linux` |
| `Ops_CentOS` | `CentosOps` | `Cyonima\Ops\Linux` |
| `Ops_Debian` | `DebianOps` | `Cyonima\Ops\Linux` |

## Method Name Changes

### Snake Case to Camel Case

All methods have been renamed from snake_case to camelCase following PSR-12:

| v1.0.0 | v2.0.0 |
|--------|--------|
| `set_proxy()` | `setProxy()` |
| `unset_proxy()` | `unsetProxy()` |
| `set_RSA()` | `setRsaAuthentication()` |
| `unset_RSA()` | `unsetRsaAuthentication()` |
| `modify_port()` | `setSshPort()` |
| `set_host()` | `setHost()` |
| `set_credentials()` | `setCredentials()` |
| `open_connection()` | `openConnection()` |
| `close_connection()` | `closeConnection()` |
| `remote_exec()` | `remoteExec()` |
| `scp_put()` | `scpPut()` |
| `scp_get()` | `scpGet()` |
| `systemctl()` | `systemctl()` (unchanged) |
| `add_user()` | `addUser()` |
| `delete_user()` | `deleteUser()` |
| `change_password()` | `changePassword()` |
| `package_install()` | `installPackage()` |
| `package_uninstall()` | `uninstallPackage()` |
| `package_upgrade()` | `upgradePackages()` |

## Error Handling Changes

### v1.0.0 (String Returns)
```php
$ops = new Ops_Ubuntu();
$ops->set_host('10.0.0.1');
$ops->set_credentials('user', 'pass');

$result = $ops->open_connection();
if ($result === "authetication succed") {
    echo "Success";
} else if ($result === "authentication failed") {
    echo "Auth failed";
}
```

### v2.0.0 (Exceptions)
```php
use Cyonima\Ops\Linux\UbuntuOps;
use Cyonima\Ops\Exception\{ConnectionException, AuthenticationException};

$ops = new UbuntuOps();
$ops->setHost('10.0.0.1')
    ->setCredentials('user', 'pass');

try {
    $ops->openConnection();
    echo "Success";
} catch (ConnectionException $e) {
    echo "Connection failed: " . $e->getMessage();
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
}
```

## API Changes

### Fluent Interface Support

v2.0.0 introduces method chaining:

```php
// v1.0.0
$ops->set_host('10.0.0.1');
$ops->set_credentials('user', 'pass');
$ops->modify_port(2222);
$ops->set_proxy('jump.example.com');
$ops->open_connection();

// v2.0.0 (preferred)
$ops->setHost('10.0.0.1')
    ->setCredentials('user', 'pass')
    ->setSshPort(2222)
    ->setProxy('jump.example.com')
    ->openConnection();
```

### Namespace Usage

You must import classes with `use` statements:

```php
// v1.0.0 - No namespaces
require_once('ops.class.php');
$ops = new Ops_Ubuntu();

// v2.0.0
use Cyonima\Ops\Linux\UbuntuOps;
$ops = new UbuntuOps();

// Or with composer autoload
// composer require cyonima/ops-lib
$ops = new \Cyonima\Ops\Linux\UbuntuOps();
```

## Global Functions

### Password Strength Check

```php
// v1.0.0
if (check_password_strength('MyPassword123!@')) {
    echo "Strong";
}

// v2.0.0
use Cyonima\Ops\Helper\UtilityHelper;
if (UtilityHelper::checkPasswordStrength('MyPassword123!@')) {
    echo "Strong";
}
```

### File Search and Replace

```php
// v1.0.0
SeekAndReplace('old', 'new', '/path/to/file.txt');

// v2.0.0
use Cyonima\Ops\Helper\UtilityHelper;
UtilityHelper::seekAndReplace('old', 'new', '/path/to/file.txt');
```

### Read from STDIN

```php
// v1.0.0
$input = ReadStdIn();

// v2.0.0
use Cyonima\Ops\Helper\UtilityHelper;
$input = UtilityHelper::readStdIn();
```

## New Exception Classes

v2.0.0 introduces specific exception types:

```php
use Cyonima\Ops\Exception\{
    OpsException,           // Base exception
    ConnectionException,     // Connection failures
    AuthenticationException, // Auth failures
    ExecutionException       // Command execution failures
};
```

## Constructor Changes

### RSA Authentication

```php
// v1.0.0
$ops->set_RSA('user', '/path/to/private_key', '/path/to/public_key');

// v2.0.0
$ops->setRsaAuthentication('user', '/path/to/private_key', '/path/to/public_key');
```

## Migration Checklist

- [ ] Update class instantiation with namespace imports
- [ ] Replace all snake_case method calls with camelCase equivalents
- [ ] Replace string return value checks with exception handling
- [ ] Update global function calls to use `UtilityHelper` class
- [ ] Test all connection scenarios (direct, proxy, RSA, password)
- [ ] Update any error handling logic to use exceptions
- [ ] Add `composer require cyonima/ops-lib` to your project

## Complete Migration Example

### Before (v1.0.0)
```php
<?php
require_once('ops.class.php');

$ops = new Ops_Ubuntu();
$ops->set_host('10.0.0.100');
$ops->set_credentials('ubuntu', 'password');

$result = $ops->open_connection();
if ($result !== "authetication succed") {
    die("Failed to connect");
}

$output = $ops->remote_exec('apt update && apt upgrade -y');
echo $output;

$ops->close_connection();
?>
```

### After (v2.0.0)
```php
<?php
use Cyonima\Ops\Linux\UbuntuOps;
use Cyonima\Ops\Exception\{ConnectionException, AuthenticationException, ExecutionException};

$ops = new UbuntuOps();
$ops->setHost('10.0.0.100')
    ->setCredentials('ubuntu', 'password');

try {
    $ops->openConnection();
    $output = $ops->remoteExec('apt update && apt upgrade -y');
    echo $output;
    $ops->closeConnection();
} catch (ConnectionException $e) {
    echo "Connection error: " . $e->getMessage();
} catch (AuthenticationException $e) {
    echo "Authentication error: " . $e->getMessage();
} catch (ExecutionException $e) {
    echo "Execution error: " . $e->getMessage();
}
?>
```

## Support

If you encounter any issues during migration, please refer to the main README.md or contact support.
