# Cyonima OPS PHP Library

[![PHPUnit](https://img.shields.io/badge/CI-GitHub%20Actions-blue.svg)](https://github.com/LudovicBoudi/Cyonima.ops.php.lib/actions/workflows/phpunit.yml)
[![Coverage Status](https://img.shields.io/badge/coverage-codecov-yellow.svg)](https://codecov.io/gh/LudovicBoudi/Cyonima.ops.php.lib)

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
- Support for multiple distributions (Ubuntu, Debian, SUSE, Fedora, Red Hat, CentOS, Rocky, Linux Mint, Zorin OS)
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

✅ **BSD System Management**
- Support for FreeBSD and OpenBSD
- Package management via `pkg` (FreeBSD) and `pkg_add` / `pkg_delete` (OpenBSD)
- User, service, and file management over SSH
- Process, network, and firewall helper methods
- Shared POSIX shell helper implementations across BSD and Linux

✅ **Azure Cloud Management**
- Basic Azure CLI management helpers over SSH
- Resource group, subscription, VM and CLI version helpers
- Remote Azure CLI execution orchestration for automation
- Support for service principal login and resource operations

✅ **OpenStack Cloud Management**
- Basic OpenStack CLI management helpers over SSH
- Project, flavor, image, network and server helpers
- Remote OpenStack execution orchestration for automation
- Support for identity-based authentication via OpenStack credentials

✅ **GCP Cloud Management**
- Basic gcloud CLI management helpers over SSH
- Project, region, zone and compute instance helpers
- Remote Google Cloud execution orchestration for automation
- Support for service account authentication and project configuration

✅ **AWS Cloud Management**
- Basic AWS CLI management helpers over SSH
- Profile, region, EC2 instance and resource helpers
- Remote AWS execution orchestration for automation
- Support for credential configuration and instance lifecycle management

✅ **KVM/libvirt Management**
- Basic virsh command-line management over SSH
- Virtual machine lifecycle helpers (create, start, stop, delete)
- Network and storage pool management
- VM resource configuration (CPU, memory)
- Support for local and remote KVM hypervisors

✅ **VirtualBox Management**
- Basic VBoxManage command-line management over SSH
- Virtual machine lifecycle helpers (create, start, stop, delete)
- Disk and network management
- VM resource configuration (CPU, memory)
- VM cloning support

✅ **Proxmox VE Management**
- Proxmox qm and pvesh command-line management over SSH
- Virtual machine and container lifecycle helpers
- Snapshot and migration support
- Cluster and storage management
- VM resource configuration (CPU, memory, disk resizing)

✅ **Windows System Management**
- PowerShell over SSH support via `WindowsOps`
- Native WinRM support via `WinRmClient` and `WindowsWinRmOps`
- Windows service, user, and file management
- Process and event log helpers
- Network and route inspection / route creation
- Firewall rule and profile management
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


### Linux Mint and Zorin OS examples

These distributions are Ubuntu derivatives and are supported via `LinuxMintOps` and `ZorinOps` which reuse the `UbuntuOps` implementations.

```php
use Cyonima\Ops\Linux\LinuxMintOps;
use Cyonima\Ops\Linux\ZorinOps;

$mint = new LinuxMintOps();
$mint->setHost('mint.example.local')->setCredentials('user','pass')->setSshPort(22);
$mint->openConnection();
$mint->installPackage('htop');
$mint->closeConnection();

$zorin = new ZorinOps();
$zorin->setHost('zorin.example.local')->setCredentials('user','pass')->setSshPort(22);
$zorin->openConnection();
$zorin->upgradePackages();
$zorin->closeConnection();
```

### FreeBSD and OpenBSD examples

Les plateformes BSD sont prises en charge via `FreeBsdOps` et `OpenBsdOps`. Ces classes implémentent un wrapper SSH/BSD qui utilise les outils de gestion de paquets et utilisateur natifs de chaque OS.

```php
use Cyonima\Ops\Bsd\FreeBsdOps;
use Cyonima\Ops\Bsd\OpenBsdOps;

$freebsd = new FreeBsdOps();
$freebsd->setHost('freebsd.example.local')->setCredentials('root','password')->setSshPort(22);
$freebsd->openConnection();
$freebsd->installPackage('nginx');
$freebsd->closeConnection();

$openbsd = new OpenBsdOps();
$openbsd->setHost('openbsd.example.local')->setCredentials('root','password')->setSshPort(22);
$openbsd->openConnection();
$openbsd->installPackage('nginx');
$openbsd->closeConnection();
```

### Azure CLI examples

Un hôte distant peut utiliser Azure CLI pour piloter les ressources Azure via SSH. Installez `az` sur la machine distante avant d'utiliser ces helpers.

```php
use Cyonima\Ops\Azure\AzureOps;

$azure = new AzureOps();
$azure->setHost('azure-proxy.example.local')->setCredentials('ops','password')->setSshPort(22);
$azure->openConnection();
$azure->loginWithServicePrincipal('TENANT_ID', 'CLIENT_ID', 'CLIENT_SECRET');
$azure->setSubscription('YOUR_SUBSCRIPTION_ID');
$azure->createResourceGroup('my-rg', 'westeurope');
$azure->createVirtualMachine('my-vm', 'my-rg');
$azure->closeConnection();
```

### OpenStack CLI examples

Un hôte distant peut utiliser le client OpenStack pour piloter des ressources OpenStack via SSH. Installez `openstack` sur la machine distante avant d'utiliser ces helpers.

```php
use Cyonima\Ops\OpenStack\OpenStackOps;

$openstack = new OpenStackOps();
$openstack->setHost('openstack-proxy.example.local')->setCredentials('ops','password')->setSshPort(22);
$openstack->openConnection();
$openstack->setOpenStackAuthentication(
    'https://openstack.example.local:5000/v3',
    'demo',
    'admin',
    'secret'
);
$openstack->authenticate();
$openstack->listServers();
$openstack->createServer('my-vm', 'Ubuntu20.04', 'm1.small', 'private-net', 'my-key');
$openstack->closeConnection();
```

### GCP CLI examples

Un hôte distant peut utiliser Google Cloud SDK pour piloter des ressources GCP via SSH. Installez `gcloud` sur la machine distante avant d'utiliser ces helpers.

```php
use Cyonima\Ops\Gcp\GcpOps;

$gcp = new GcpOps();
$gcp->setHost('gcp-proxy.example.local')->setCredentials('ops','password')->setSshPort(22);
$gcp->openConnection();
$gcp->authenticateWithServiceAccountKey('/tmp/service-account.json');
$gcp->setProject('my-gcp-project');
$gcp->applyProject();
$gcp->listInstances('europe-west1-b');
$gcp->createInstance('my-instance', 'europe-west1-b', 'e2-medium', 'ubuntu-2004-lts', 'ubuntu-os-cloud', 'default');
$gcp->closeConnection();
```

### AWS CLI examples

Un hôte distant peut utiliser AWS CLI pour piloter des ressources AWS via SSH. Installez `aws` sur la machine distante avant d'utiliser ces helpers.

```php
use Cyonima\Ops\Aws\AwsOps;

$aws = new AwsOps();
$aws->setHost('aws-proxy.example.local')->setCredentials('ops','password')->setSshPort(22);
$aws->setProfile('default');
$aws->setRegion('eu-west-1');
$aws->openConnection();
$aws->configureCredentials('AKIAEXAMPLE', 'secret-key', 'eu-west-1');
$aws->listInstances('eu-west-1a');
$aws->createInstance('my-instance', 'ami-12345678', 't3.micro', 'subnet-01234567', 'my-key', 'sg-01234567');
$aws->closeConnection();
```

### KVM/libvirt management examples

Un hôte KVM distant peut être géré via `virsh` sur SSH. Assurez-vous que libvirt est installé et accessible sur la machine distante.

```php
use Cyonima\Ops\Kvm\KvmOps;

$kvm = new KvmOps();
$kvm->setHost('kvm-host.example.local')->setCredentials('root','password')->setSshPort(22);
$kvm->openConnection();
$kvm->listVMs();
$kvm->createVm('my-vm', '2', '2048', '/var/lib/libvirt/images/my-vm.qcow2');
$kvm->startVm('my-vm');
$kvm->getVmStatus('my-vm');
$kvm->setVmCpu('my-vm', '4');
$kvm->setVmMemory('my-vm', '4096');
$kvm->stopVm('my-vm');
$kvm->closeConnection();
```

### VirtualBox management examples

Un hôte VirtualBox distant peut être géré via `VBoxManage` sur SSH. Assurez-vous que VirtualBox est installé et accessible sur la machine distante.

```php
use Cyonima\Ops\Virtualbox\VirtualboxOps;

$vbox = new VirtualboxOps();
$vbox->setHost('vbox-host.example.local')->setCredentials('vboxuser','password')->setSshPort(22);
$vbox->openConnection();
$vbox->listVMs();
$vbox->listRunningVMs();
$vbox->createVm('test-vm', 'Ubuntu_64', '2048', '2');
$vbox->createAndAttachDisk('test-vm', '/var/vbox/test-vm.vdi', '40000');
$vbox->startVm('test-vm', 'headless');
$vbox->setVmCpu('test-vm', '4');
$vbox->setVmMemory('test-vm', '4096');
$vbox->stopVm('test-vm');
$vbox->closeConnection();
```

### Proxmox VE management examples

Un cluster Proxmox distant peut être géré via `qm` et `pvesh` sur SSH. Assurez-vous que les outils Proxmox sont installés et accessibles.

```php
use Cyonima\Ops\Proxmox\ProxmoxOps;

$proxmox = new ProxmoxOps();
$proxmox->setHost('proxmox-node.example.local')->setCredentials('root','password')->setSshPort(22);
$proxmox->openConnection();
$proxmox->getProxmoxVersion();
$proxmox->listNodes();
$proxmox->listVMs();
$proxmox->createVm('100', 'test-vm', '2048', '2', 'local:0');
$proxmox->startVm('100');
$proxmox->setVmCpu('100', '4');
$proxmox->setVmMemory('100', '4096');
$proxmox->createSnapshot('100', 'backup-2026-06-01');
$proxmox->stopVm('100');
$proxmox->closeConnection();
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
    $version = $windows->getWindowsVersion();
    echo $version->getStdout();

    $file = $windows->readFile('C:\\temp\\example.log');
    echo $file->getStdout();

    $windows->writeFile('C:\\temp\\message.txt', 'Hello from Cyonima');
    $rules = $windows->getFirewallRules();
    echo $rules->getStdout();

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
| Linux Mint | apt (Ubuntu derivative) | `LinuxMintOps` | ✅ Supported |
| Zorin OS | apt (Ubuntu derivative) | `ZorinOps` | ✅ Supported |

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

## Windows Advanced Modules

The project includes higher-level Windows modules for common infrastructure tasks. These modules are implemented as small helpers that call PowerShell cmdlets remotely (SSH or WinRM) and return `RemoteCommandOutput` objects.

- `IISOps`: install and manage IIS sites and application pools (`installIIS`, `createWebsite`, `startWebsite`, `stopWebsite`, `recycleAppPool`).
- `SQLServerOps`: run T-SQL queries via `Invoke-Sqlcmd` (`runQuery`).
- `HyperVOps`: basic Hyper-V VM lifecycle helpers (`createVM`, `startVM`, `stopVM`, `removeVM`).
- Active Directory helpers in `AbstractWindowsOps`: `joinDomain`, `createAdUser`, `addAdUserToGroup`, `createOrganizationalUnit`.
- `ExchangeOps` and `SharePointOps`: skeleton helpers for Exchange/SharePoint management (requires target server modules and privileges).

Example: install IIS and create a website

```php
use Cyonima\Ops\Windows\IISOps;

$iis = new IISOps();
$iis->setHost('windows.example.local')->setCredentials('Administrator','password')->setSshPort(22);
$iis->openConnection();
$iis->installIIS();
$iis->createWebsite('MySite', 'C:\\inetpub\\wwwroot\\mysite', 8080);
$iis->startWebsite('MySite');
$iis->closeConnection();
```

Example: run a SQL query

```php
use Cyonima\Ops\Windows\SQLServerOps;

$sql = new SQLServerOps();
$sql->setHost('sql.example.local')->setCredentials('sa','secret')->setSshPort(22);
$sql->openConnection();
$sql->runQuery('localhost\\SQLEXPRESS', 'master', 'SELECT TOP 1 name FROM sys.databases');
$sql->closeConnection();
```

Example: join AD domain

```php
use Cyonima\Ops\Windows\WindowsOps;

$win = new WindowsOps();
$win->setHost('winjoin.example.local')->setCredentials('Administrator','password')->setSshPort(22);
$win->openConnection();
$win->joinDomain('corp.example.local', 'corp\\admin', 'AdminP@ssw0rd');
$win->closeConnection();
```

Security note: these helpers invoke powerful system cmdlets and often require elevated privileges on the target host. Ensure you run them in a controlled environment and validate inputs to avoid accidental changes.

## Documentation

Complete documentation is available in multiple languages:

- **[manual.md](manual.md)** — Full documentation in French (Français)
- **[MANUAL.md](MANUAL.md)** — Full documentation in English

Both documents cover installation, architecture, API references, security best practices, troubleshooting, and comprehensive examples for all supported platforms.

## Testing

Run the test suite:

```bash
composer install
vendor/bin/phpunit --configuration phpunit.xml
```

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Ensure tests pass and code follows PSR-12
4. Submit a pull request

## License

This library is provided as-is. See LICENSE for details.

## Support

For issues, questions, or contributions, please open an issue on the project repository.


