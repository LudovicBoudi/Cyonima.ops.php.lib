# Cyonima OPS PHP Library

A modern, well-structured PHP library for infrastructure operations including SSH/SCP connectivity and automated management of Cisco network equipment and various Linux distributions.

## Features

✅ **SSH/SCP Operations**
- Password and RSA key authentication
- Direct connections and proxy (jump host) support
- Remote command execution with full output capture
- Secure file transfer (SCP put/get)

✅ **Network Equipment Management**
- Cisco switch/router configuration
- VLAN management
- Interface configuration
- ACL configuration
- NetFlow monitoring setup
- Route management

✅ **Linux System Management**
- Support for multiple distributions (Ubuntu, Debian, SUSE, Fedora, Red Hat, CentOS, Rocky)
- User account management
- Service management (systemctl)
- Package installation/removal/upgrade
- File management (read/write/copy/move/remove)
- Process management and monitoring
- Log and journal access
- Network and route management
- Firewall support (ufw/firewall-cmd)
- SELinux and AppArmor status management
- Password management
- Privilege escalation (sudo) support

✅ **Windows System Management**
- PowerShell over SSH support via `WindowsOps`
- Native WinRM support via `WinRmClient` and `WindowsWinRmOps`
- Windows service, user, and package management
- `winget` / `choco` package support

✅ **macOS System Management**
- PowerShell command execution via SSH
- Homebrew package installation, removal, and upgrade
- Service control via `brew services`
- Local user management via `sysadminctl`
- System version reporting

✅ **Modern PHP Standards**
- PSR-4 autoloading
- Type-safe interfaces
- Proper exception handling
- Fluent API design
- Comprehensive PHPDoc documentation

## Installation

### Via Composer

```bash
composer require cyonima/ops-lib
```

### Requirements

- PHP 8.0 or higher
- SSH2 PHP extension (`php-ssh2`)
- cURL PHP extension (`ext-curl`) for WinRM support
- Network connectivity to target infrastructure

### Windows Host Preparation

For Windows remote management, the target host must be prepared as follows:

- Enable OpenSSH Server for PowerShell-over-SSH access, or install/configure native WinRM.
- If using WinRM, allow HTTP/HTTPS traffic on port `5985`/`5986` and configure the Windows WinRM listener.
- Ensure the account has sufficient privileges for service, user, and package management operations.
- Install `winget` or `Chocolatey` if you want package installation support on Windows.

## Quick Start

### Basic SSH Connection and Command Execution

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setHost('192.168.1.100')
    ->setCredentials('ubuntu', 'password')
    ->setSshPort(22);

try {
    $ops->openConnection();
    $output = $ops->remoteExec('uname -a');
    echo $output;
    $ops->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Using RSA Key Authentication

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setHost('192.168.1.100')
    ->setRsaAuthentication('ubuntu', '/home/user/.ssh/id_rsa', '/home/user/.ssh/id_rsa.pub')
    ->setSshPort(2222);

try {
    $ops->openConnection();
    $output = $ops->installPackage('nginx');
    $ops->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Linux file and process helpers

```php
use Cyonima\Ops\Linux\UbuntuOps;

$linux = new UbuntuOps();
$linux->setHost('192.168.1.100')
    ->setCredentials('ubuntu', 'password')
    ->setSshPort(22);

try {
    $linux->openConnection();
    $read = $linux->readFile('/var/log/syslog');
    echo $read->getStdout();

    $linux->writeFile('/tmp/message.txt', 'Hello from Cyonima');
    $linux->listProcesses('sshd');
    $linux->getFirewallStatus();
    $linux->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Windows PowerShell over SSH

```php
use Cyonima\Ops\Windows\WindowsOps;

$windows = new WindowsOps();
$windows->setHost('windows.example.local')
    ->setCredentials('Administrator', 'password')
    ->setSshPort(22);

try {
    $windows->openConnection();
    $output = $windows->getWindowsVersion();
    echo $output->getStdout();
    $windows->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Windows native WinRM

```php
use Cyonima\Ops\Windows\WinRmClient;
use Cyonima\Ops\Windows\WindowsWinRmOps;

$client = new WinRmClient('windows.example.local', 'Administrator', 'password');
$winrm = new WindowsWinRmOps($client);

try {
    $output = $winrm->getWindowsVersion();
    echo $output->getStdout();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### macOS PowerShell over SSH

```php
use Cyonima\Ops\MacOS\MacOsOps;

$mac = new MacOsOps();
$mac->setHost('mac.example.local')
    ->setCredentials('admin', 'password')
    ->setSshPort(22);

try {
    $mac->openConnection();
    $output = $mac->getMacOsVersion();
    echo $output->getStdout();
    $mac->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### RemoteCommandOutput Usage

The library now returns a `RemoteCommandOutput` object for command execution methods. It exposes stdout, stderr, exit code, and helper methods:

```php
$output = $ops->remoteExec('uname -a');
if ($output->isSuccessful()) {
    echo $output->getStdout();
} else {
    echo "Command failed (exit " . $output->getExitCode() . "): " . $output->getStderr();
}

$output->throwIfFailed();
```

### Using a Proxy/Jump Host

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setProxy('10.0.0.1')  // Jump host
    ->setHost('192.168.1.100')  // Final target
    ->setCredentials('user', 'password');

try {
    $ops->openConnection();
    $output = $ops->remoteExec('systemctl status apache2');
    $ops->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Cisco Network Device Configuration

```php
use Cyonima\Ops\Network\CiscoOps;

$cisco = new CiscoOps();
$cisco->setHost('10.0.0.10')
    ->setCredentials('admin', 'password');

try {
    $cisco->openConnection();
    
    // Configure VLAN
    $output = $cisco->configureVlan('10', 'Management', 'Gi0/1');
    
    // Set IP on VLAN interface
    $cisco->setVlanIpAddress('10', 'Management VLAN', '10.0.0.1', '255.255.255.0');
    
    // Add route
    $cisco->addRoute('192.168.0.0', '255.255.0.0', '10.0.0.254', '1');
    
    $cisco->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Class Hierarchy

```
AbstractOps (base class)
├── Network/
│   └── CiscoOps
└── Linux/
    ├── AbstractLinuxOps
    ├── UbuntuOps
    ├── DebianOps
    ├── SuseOps
    ├── FedoraOps
    ├── RedhatOps
    ├── CentosOps
    └── RockyOps
└── Windows/
    ├── AbstractWindowsOps
    ├── WindowsOps
    ├── WinRmClient
    └── WindowsWinRmOps
└── MacOS/
    ├── AbstractMacOsOps
    └── MacOsOps
```

## Exception Handling

The library uses custom exceptions for better error handling:

```php
use Cyonima\Ops\Exception\{
    ConnectionException,
    AuthenticationException,
    ExecutionException,
    OpsException
};

try {
    $ops->openConnection();
} catch (ConnectionException $e) {
    echo "Connection failed: " . $e->getMessage();
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (ExecutionException $e) {
    echo "Command execution failed: " . $e->getMessage();
} catch (OpsException $e) {
    echo "General OPS error: " . $e->getMessage();
}
```

## Windows Support

The library now supports Windows management using two modes:

- `WindowsOps`/`AbstractWindowsOps` for PowerShell over SSH
- `WinRmClient` and `WindowsWinRmOps` for native WinRM over HTTP(S)

Windows support includes:

- service control and status
- local user creation, deletion, and password management
- package installation/uninstallation via `winget` or `choco`
- system version reporting

## Utility Helpers

The library includes helper functions for common tasks:

```php
use Cyonima\Ops\Helper\UtilityHelper;

// Check password strength
if (UtilityHelper::checkPasswordStrength('MyP@ssw0rdSecure1')) {
    echo "Password is strong";
}

// Search and replace in file
UtilityHelper::seekAndReplace('old_value', 'new_value', '/path/to/file.txt');

// Read from stdin
$input = UtilityHelper::readStdIn(128);
```

## Fluent Interface

All configuration methods support a fluent interface for cleaner code:

```php
$ops->setHost('10.0.0.1')
    ->setCredentials('admin', 'pass')
    ->setSshPort(2222)
    ->setProxy('jump.example.com')
    ->openConnection();
```

## Security Considerations

⚠️ **Important**: This library does NOT automatically sanitize input variables to prevent injection attacks. It is the developer's responsibility to validate and sanitize all user inputs before passing them to this library.

### Best Practices:
- Always validate input before passing to library methods
- Use parameterized queries when available
- Store credentials securely (environment variables, secure vaults)
- Use RSA key authentication instead of passwords when possible
- Restrict SSH access with security groups and firewall rules
- Use strong, unique passwords for all accounts

## Linux Distribution Support

| Distribution | Package Manager | Class | Status |
|-------------|-----------------|-------|--------|
| Ubuntu | apt | `UbuntuOps` | ✅ Supported |
| Debian | apt | `DebianOps` | ✅ Supported |
| SUSE/openSUSE | zypper | `SuseOps` | ✅ Supported |
| Fedora | dnf | `FedoraOps` | ✅ Supported |
| Red Hat / RHEL | yum | `RedhatOps` | ✅ Supported |
| CentOS | yum/dnf | `CentosOps` | ✅ Supported |
| Rocky Linux | dnf | `RockyOps` | ✅ Supported |

## API Documentation

Full API documentation is available through PHPDoc in the source code. Key methods:

### Connection Management
- `setHost(string $host): self`
- `setCredentials(string $username, string $password): self`
- `setRsaAuthentication(string $username, string $privateKey, string $publicKey): self`
- `setSshPort(int $port): self`
- `setProxy(string $proxyHost): self`
- `openConnection(): void`
- `closeConnection(): void`

### Command Execution
- `remoteExec(string $command): string`
- `scpPut(string $localPath, string $remotePath, string $permissions): void`
- `scpGet(string $remotePath, string $localPath): void`

## Development

### Running Tests
```bash
composer test
```

### Code Analysis
```bash
composer analyze
```

### Fix Code Style
```bash
composer fix
```

## Changelog

### Version 2.0.0 (May 2026)
- Complete rewrite using modern PHP 8.0+ standards
- Added PSR-4 autoloading and namespace support
- Implemented proper exception handling
- Added fluent interface for configuration
- Full PHPDoc documentation
- Separated concerns into multiple classes
- Improved error messages and logging
- Fixed typos in method names (authetication → authentication)

### Version 1.0.0 (Legacy)
- Original monolithic implementation

## License

MIT License. See LICENSE file for details.

## Contributing

Contributions are welcome. Please ensure code follows PSR-12 standards and includes proper documentation.

## Support

For issues, questions, or contributions, please contact the development team or submit an issue in the repository.

