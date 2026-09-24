# Complete Manual — Cyonima OPS PHP Library

Last updated: June 1, 2026

This manual is comprehensive documentation for the Cyonima OPS library. It provides
in-depth information for installation, configuration, architecture understanding,
API usage, operational examples, security best practices, static analysis, test
execution, and continuous integration.

## Table of Contents
- [1. Overview](#1-overview)
- [2. Installation and Prerequisites](#2-installation-and-prerequisites)
- [3. Architecture and Key Concepts](#3-architecture-and-key-concepts)
- [4. Modules and Detailed APIs](#4-modules-and-detailed-apis)
- [5. Detailed Usage and Examples](#5-detailed-usage-and-examples)
- [6. Security, Validation and Escaping](#6-security-validation-and-escaping)
- [7. Static Analysis (PHPStan)](#7-static-analysis-phpstan)
- [8. Testing, Coverage and CI](#8-testing-coverage-and-ci)
- [9. Troubleshooting and Diagnostics](#9-troubleshooting-and-diagnostics)
- [10. Contributing and Publishing](#10-contributing-and-publishing)
- [11. Appendices](#11-appendices)

---

## 1. Overview

Cyonima OPS is a high-level PHP library designed for infrastructure administration
automation via SSH/SCP/SFTP and WinRM. It encapsulates recurring operations (package
management, users, services, firewall, network, virtualization, cloud, etc.) behind
typed and testable helpers.

The library prioritizes:
- Clear and typed API usage (PHP 8.1+)
- Systematic argument escaping
- Separation of concerns (ops by OS / module)
- PSR-3 logging compatibility
- Secure password handling (never exposed in process list)
- Secure file transfers via SCP and SFTP

### Module tree

```
Cyonima\Ops\
├── AbstractOps              # Base class: SSH, SCP, SFTP, auth
├── RemoteCommandOutput      # Encapsulated return value
├── InputValidator           # Input validation
├── Traits\
│   ├── UnixFileTrait        # File operations (Linux + BSD)
│   ├── Linux\
│   │   ├── LinuxServiceTrait
│   │   ├── LinuxUserTrait
│   │   └── LinuxSystemTrait
│   └── Bsd\
│       ├── BsdServiceTrait
│       ├── BsdUserTrait
│       └── BsdNetworkTrait
├── Linux\
│   ├── AbstractLinuxOps     # abstract, uses 4 traits
│   ├── UbuntuOps            # apt
│   ├── RedhatOps            # yum/dnf
│   ├── SuseOps              # zypper
│   ├── LinuxMintOps         # apt (inherits UbuntuOps)
│   └── ZorinOps             # apt (inherits UbuntuOps)
├── Bsd\
│   ├── AbstractBsdOps       # abstract, uses 4 traits
│   ├── FreeBsdOps           # pkg
│   └── OpenBsdOps           # pkg_add/pkg_delete
├── Windows\
│   ├── AbstractWindowsOps   # PowerShell over SSH
│   ├── WindowsOps           # concrete implementation
│   ├── WindowsWinRmOps      # Native WinRM (SOAP/cURL)
│   ├── WinRmClient          # SOAP WinRM client
│   ├── IISOps               # IIS manager
│   ├── SQLServerOps         # SQL Server queries
│   └── HyperVOps            # Hyper-V manager
├── MacOS\
│   └── MacOsOps             # brew, sysadminctl
├── Network\
│   ├── CiscoOps             # Cisco IOS/IOS-XE
│   ├── JuniperOps           # Juniper JunOS
│   ├── MikrotikOps          # MikroTik RouterOS
│   ├── HuaweiVrpOps         # Huawei VRP
│   ├── PaloAltoOps          # Palo Alto PAN-OS
│   ├── FortinetOps          # Fortinet FortiOS
│   └── CheckPointOps        # Check Point Gaia
├── Aws\
│   └── AwsOps               # AWS CLI via SSH
├── Azure\
│   └── AzureOps             # Azure CLI via SSH
├── Gcp\
│   └── GcpOps               # gcloud CLI via SSH
├── OpenStack\
│   └── OpenStackOps         # openstack CLI via SSH
├── Kvm\
│   └── KvmOps               # virsh CLI
├── Virtualbox\
│   └── VirtualboxOps        # VBoxManage CLI
├── Docker\
│   └── DockerOps            # containers, compose, images
├── Database\
│   ├── AbstractDatabaseOps  # abstract
│   ├── MySqlOps             # mysql, mysqldump
│   └── PostgreSqlOps        # psql, pg_dump, pg_restore
├── WebServer\
│   ├── AbstractWebServerOps # abstract
│   ├── NginxOps             # nginx, vhosts, logs
│   └── ApacheOps            # apache2ctl, a2ensite
├── System\
│   ├── SystemOps            # inventory (CPU, disk, etc.)
│   ├── CronOps              # crontab management
│   └── SshKeyOps            # authorized_keys
├── Kubernetes\
│   └── KubernetesOps        # kubectl, helm
├── LoadBalancer\
│   └── HaProxyOps           # HAProxy stats, servers
├── Ssl\
│   └── CertbotOps           # Let's Encrypt certificates
├── Backup\
│   └── RsyncOps             # rsync, backups
├── Vmware\
│   └── VmwareOps            # vim-cmd, esxcli (ESXi)
├── Kvm\
│   ├── AbstractKvmOps
│   └── KvmOps               # virsh CLI
├── Virtualbox\
│   └── VirtualboxOps        # VBoxManage
└── Proxmox\
    └── ProxmoxOps           # qm + pvesh CLI
```

## 2. Installation and Prerequisites

### System requirements

- PHP **8.1 or higher** (requires `Stringable` and `readonly` properties)
- Recommended PHP extensions:
  - `ext-ssh2` — SSH connections, SCP, SFTP
  - `ext-curl` — WinRM (SOAP over HTTPS)
  - `ext-mbstring` — multibyte string manipulation
- Composer — dependency management

### Installation

```bash
composer require cyonima/ops-lib
```

Install development dependencies:

```bash
composer install --no-interaction
```

### Minimal configuration

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setHost('192.0.2.10')
    ->setCredentials('admin', 'password')
    ->setSshPort(22);
$ops->openConnection();
// ...
$ops->closeConnection();
```

## 3. Architecture and Key Concepts

### 3.1 `AbstractOps`

Base class for the entire library. Manages SSH connections, authentication,
remote command execution, and file transfers.

#### Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `DEFAULT_SSH_PORT` | `int` | `22` | Default SSH port |
| `DEFAULT_SSH_TIMEOUT` | `int` | `30` | Connection timeout (seconds) |

#### Configuration methods

| Method | Returns | Description |
|--------|---------|-------------|
| `setHost(string $host)` | `self` | Set the target host |
| `setSshPort(int $port)` | `self` | SSH port (1-65535) |
| `setSshTimeout(int $seconds)` | `self` | Connection timeout (min 1s) |
| `setCredentials(string $username, string $password)` | `self` | Password authentication |
| `setRsaAuthentication(string $username, string $privateKey, string $publicKey)` | `self` | RSA key authentication |
| `setAgentAuthentication(string $username)` | `self` | SSH agent authentication |
| `setProxy(string $proxyHost)` | `self` | SSH jump host |
| `unsetProxy()` | `self` | Disable proxy |
| `setLogger(LoggerInterface $logger)` | `self` | Custom PSR-3 logger |
| `setStrictHostKeyChecking(bool $strict)` | `self` | Enable/disable strict host key verification |
| `setKnownHostsFile(string $path)` | `self` | Path to known_hosts file |
| `applySshConfig(string $host, ?string $configFile)` | `self` | Apply ~/.ssh/config settings for a host |

Authentication priority order: **SSH agent → RSA key → password**.

#### Connection / disconnection

| Method | Description |
|--------|-------------|
| `openConnection(): void` | Open SSH connection (throws `ConnectionException`, `AuthenticationException`) |
| `closeConnection(): void` | Close connection |
| `isConnected(): bool` | Check if a connection is active |
| `getHostFingerprint(): ?string` | Server's SHA-1 host key fingerprint (available after connect) |

#### SSH config

| Method | Description |
|--------|-------------|
| `parseSshConfig(string $host, ?string $configFile): array` (static) | Parse ~/.ssh/config and return settings (hostname, port, user, identityFile, proxyJump) |
| `applySshConfig(string $host, ?string $configFile): self` | Apply SSH config settings to the current instance |

#### Command execution

| Method | Returns | Description |
|--------|---------|-------------|
| `remoteExec(string $command)` | `RemoteCommandOutput` | Execute a remote SSH command |

#### File transfer

| Method | Description |
|--------|-------------|
| `scpPut(string $local, string $remote, string $permissions = '0644')` | Send via SCP |
| `scpGet(string $remote, string $local)` | Receive via SCP |
| `sftpPut(string $local, string $remote)` | Send via SFTP |
| `sftpGet(string $remote, string $local)` | Receive via SFTP (streaming SSH2) |

> **SFTP note**: the SFTP subsystem is initialized on first use (`initializeSftp()`)
> and reused for subsequent transfers.

### 3.2 `RemoteCommandOutput`

Immutable structure encapsulating remote command results.

```php
$result = $ops->remoteExec('uname -a');
echo $result->getStdout();       // string
echo $result->getStderr();       // string
echo $result->getExitCode();     // int
$result->isSuccessful();         // bool — exit === 0
$result->throwIfFailed();        // throw ExecutionException on failure
```

### 3.3 `InputValidator` and escaping

`InputValidator` provides static validation methods:

| Method | Description |
|--------|-------------|
| `validateHost(string)` | Validate host (IP or hostname) |
| `validateUsername(string)` | Validate username |
| `validatePassword(string, int $minLength)` | Minimum length check |
| `validateSshPort(int)` | Port between 1 and 65535 |
| `validateFileReadable(string)` | Local file is readable |
| `validateCommand(string, bool $strict)` | Shell injection detection |
| `validateVlanNumber(string)` | VLAN between 1-4094 |
| `validateIpAddress(string, bool $allowCidr)` | Valid IP address |
| `sanitizeFilename(string)` | Sanitize a filename |

Escaping functions:

- `escapeShellArgument(string $s): string` — uses `escapeshellarg()` for POSIX shells
- `escapePowerShellArgument(string $s): string` — single quote + doubling for PowerShell

### 3.4 Logging (PSR-3)

`SimpleLogger` implements PSR-3 and writes to `stderr` by default.
You can inject any PSR-3 logger:

```php
$ops->setLogger(new class extends AbstractLogger { ... });
```

## 4. Modules and Detailed APIs

### 4.1 Linux (`AbstractLinuxOps`)

`AbstractLinuxOps` uses 4 traits to organize its methods:

#### LinuxServiceTrait — systemd service management

```php
$ops->systemctl('nginx', 'status');
$ops->systemctlWithPrivilege('nginx', 'restart', $sudoPassword);
$ops->systemctlWithPrivilegeNoPwd('nginx', 'start');
```

#### LinuxUserTrait — User management

```php
$ops->addUser('jdoe', 'P@ssw0rd', 'staff');          // optional group
$ops->addUserWithPrivilege('jdoe', 'P@ssw0rd', $sudoPassword);
$ops->deleteUser('jdoe');
$ops->deleteUserWithPrivilege('jdoe', $sudoPassword);
$ops->changePassword('jdoe', 'NewP@ss');
$ops->changePasswordWithPrivilege('jdoe', 'NewP@ss', $sudoPassword);
```

#### LinuxSystemTrait — Processes, logs, network, firewall, security

```php
// Processes
$ops->listProcesses();                                // or listProcesses('apache')
$ops->killProcess(1234);                              // SIGTERM (kill -15)
$ops->killProcessByName('nginx');                     // pkill -f
$ops->getTopProcesses(10);

// Logs
$ops->readLogFile('/var/log/syslog', 100);            // tail -n 100
$ops->readJournalLog('nginx.service', 50);
$ops->tailJournalLog('nginx.service', 20);

// Network
$ops->getNetworkInterfaces();
$ops->getRoutingTable();
$ops->addNetworkRoute('10.0.0.0/8', '192.168.1.1', 'eth0');

// Firewall
$ops->getFirewallStatus();                            // ufw status / firewall-cmd
$ops->enableFirewall();
$ops->disableFirewall();
$ops->addFirewallRule('443', 'tcp');

// SELinux
$ops->getSelinuxStatus();
$ops->setSelinuxMode('Enforcing');

// AppArmor
$ops->getAppArmorStatus();
$ops->enforceAppArmorProfile('my-profile');
```

The firewall auto-detects `ufw` or `firewall-cmd` on the target.

#### UnixFileTrait — File operations

```php
$ops->readFile('/etc/hosts');
$ops->writeFile('/tmp/test.txt', "content\n");
$ops->writeFileWithPrivilege('/etc/hosts', "127.0.0.1 localhost\n", $sudoPassword);
$ops->copyFile('/tmp/a', '/tmp/b');
$ops->copyFileWithPrivilege('/etc/a', '/etc/b', $sudoPassword);
$ops->moveFile('/tmp/a', '/tmp/b');
$ops->removeFile('/tmp/old.log');
$ops->removeFileWithPrivilege('/var/log/old.log', $sudoPassword);
$ops->makeDirectory('/app/data', '0750');
$ops->changePermissions('/app/data', '0755');
$ops->changeOwner('/app/data', 'www-data', 'www-data');
```

The `writeFile` / `writeFileWithPrivilege` methods use `base64` to transfer
content without escaping issues.

#### Package management (abstract)

`AbstractLinuxOps` declares abstract methods for package management.
Each concrete distribution implements them:

- `UbuntuOps`, `LinuxMintOps`, `ZorinOps` → `apt`
- `RedhatOps` → `yum` / `dnf`
- `SuseOps` → `zypper`

```php
// Example with UbuntuOps
$ops->installPackage('nginx');
$ops->installPackageWithPrivilege('nginx', $sudoPassword);
$ops->uninstallPackage('apache2');
$ops->upgradePackages();                              // apt update && apt upgrade -y
```

#### executeWithSudo() — secure sudo execution

The `executeWithSudo()` method protects the sudo password using a temporary file
with restricted permissions:

```php
// Instead of: echo password | sudo -S command  (visible in `ps aux`)
// The method does:
//   1. base64_encode(password) → temp file /tmp/._sudo_<random>
//   2. chmod 400 on the file
//   3. sudo -S command < file
//   4. rm -f the file
```

### 4.2 Windows

#### PowerShell over SSH (`AbstractWindowsOps`, `WindowsOps`)

All commands are wrapped by `toPowerShellCommand()`:

```php
// Internally: powershell -NoProfile -NonInteractive -Command '<escaped script>'
```

Available helpers:

```php
$win->readFile('C:\\temp\\log.txt');
$win->writeFile('C:\\temp\\out.txt', 'content');
$win->getProcesses();                                 // ConvertTo-Json
$win->killProcessById(1234);
$win->getEventLog('Application', 50);
$win->getNetworkInterfaces();
$win->getFirewallProfile();
$win->addFirewallRule('AllowSSH', 'description', 'Inbound', 'TCP', '22');
```

Specialized Windows modules:

| Class | Description |
|-------|-------------|
| `WindowsOps` | Generic Windows helpers |
| `IISOps` | IIS management (sites, application pools) |
| `SQLServerOps` | SQL query execution |
| `HyperVOps` | Hyper-V management (VM creation, etc.) |
| `WindowsWinRmOps` | Native WinRM (SOAP) |

```php
// Hyper-V
$hv = new HyperVOps();
$hv->createVM('vm1', 2048, 'C:\\vhd\\vm1.vhdx');
$hv->startVM('vm1');

// SQL Server
$sql = new SQLServerOps();
$sql->runQuery('localhost\\SQLEXPRESS', 'master', 'SELECT 1;');
```

Active Directory:

```php
$win->joinDomain('corp.example.local', 'corp\\admin', 'P@ssw0rd');
$win->createAdUser('jdoe', 'John Doe', 'P@ssw0rd', 'OU=Users,DC=corp,DC=local');
$win->addAdUserToGroup('jdoe', 'Domain Admins');
```

#### Native WinRM (`WinRmClient`, `WindowsWinRmOps`)

For environments without SSH access:

```php
use Cyonima\Ops\Windows\WinRmClient;
use Cyonima\Ops\Windows\WindowsWinRmOps;

$client = new WinRmClient('srv-windows.example.local', 'Administrator', 'password');
$client->setUseHttps(true);                           // WinRM over HTTPS (5986)
$winrm = new WindowsWinRmOps($client);
$version = $winrm->getWindowsVersion();
```

### 4.3 macOS (`MacOsOps`)

```php
$mac = new MacOsOps();
$mac->getMacOsVersion();                              // sw_vers -productVersion
$mac->installPackage('nginx');                        // brew install
$mac->manageService('nginx', 'start');                // brew services
$mac->addUser('jdoe', 'P@ssw0rd');                   // sysadminctl
$mac->getFirewallStatus();                            // /usr/libexec/ApplicationFirewall
$mac->enableFirewall();
```

### 4.4 Network (`Cyonima\Ops\Network`)

Seven classes for network device automation, all extending `AbstractOps`.
Connection follows the same pattern for every vendor:

```php
use Cyonima\Ops\Network\CiscoOps;

$device = new CiscoOps();
$device->setHost('switch.example.local')
       ->setCredentials('admin', 'secret');
$device->openConnection();
$result = $device->getVersion();     // RemoteCommandOutput
```

#### 4.4.1 CiscoOps — Cisco IOS/IOS-XE (56 methods)

```php
// Show commands
$cisco->getHostname();
$cisco->getVersion();
$cisco->getRunningConfig();
$cisco->getInterfaces();
$cisco->getVlans();
$cisco->getIpRoute();
$cisco->getCdpNeighbors();
$cisco->getLldpNeighbors();

// VLAN configuration
$cisco->createVlan(10, 'Users');
$cisco->deleteVlan(10);
$cisco->setInterfaceAccessVlan('GigabitEthernet0/1', 10);
$cisco->setInterfaceTrunk('GigabitEthernet0/24', '10,20', '1');

// Interface configuration
$cisco->setInterfaceDescription('GigabitEthernet0/1', 'Link to Core');
$cisco->setInterfaceIp('GigabitEthernet0/1', '192.168.1.1', '255.255.255.0');
$cisco->createVlanInterface(10, '192.168.10.1', '255.255.255.0');
$cisco->shutdownInterface('GigabitEthernet0/2');
$cisco->noShutdownInterface('GigabitEthernet0/2');

// Routing
$cisco->addStaticRoute('0.0.0.0', '0.0.0.0', '192.168.1.254');
$cisco->removeStaticRoute('10.0.0.0', '255.0.0.0', '192.168.1.1');
$cisco->setDefaultGateway('192.168.1.254');

// Security
$cisco->enableSsh('domain.local');
$cisco->createUser('jdoe', 'P@ssw0rd', 15);
$cisco->addAclRule(100, 'deny', '10.0.0.0 0.0.0.255');
$cisco->applyAclInbound('GigabitEthernet0/1', '100');

// Services
$cisco->setNtpServer('pool.ntp.org');
$cisco->setSnmpCommunity('public', 'ro');
$cisco->setSyslogServer('192.168.1.100');
$cisco->saveConfig();

// Diagnostics
$cisco->ping('8.8.8.8');
$cisco->traceroute('8.8.8.8');
$cisco->getUptime();
$cisco->getLogs();
$cisco->getEnvironment();
$cisco->exec('show ip interface brief');
```

#### 4.4.2 JuniperOps — Juniper JunOS (57 methods)

```php
// Show commands
$junos->getHostname();
$junos->getVersion();
$junos->getConfiguration();
$junos->getConfigurationSection('interfaces');
$junos->getInterfaces();
$junos->getVlans();
$junos->getRouteTable();
$junos->getArpTable();
$junos->getLldpNeighbors();

// VLAN configuration
$junos->createVlan('100', 'DMZ', 'vlan.100', '10.0.100.1/24');
$junos->deleteVlan('DMZ');
$junos->setInterfaceAccessVlan('ge-0/0/1', 'DMZ');
$junos->setInterfaceTrunk('ge-0/0/48', ['DMZ', 'Users']);

// Interface configuration
$junos->setInterfaceDescription('ge-0/0/1', 'Link to Core');
$junos->setInterfaceIp('ge-0/0/0', '192.168.1.1/24');
$junos->enableInterface('ge-0/0/2');
$junos->disableInterface('ge-0/0/2');

// Routing and security
$junos->addStaticRoute('0.0.0.0/0', '192.168.1.254');
$junos->addFirewallFilter('protect', 'term1', 'reject', '10.0.0.0/8');
$junos->applyFilterInput('ge-0/0/0', 'protect');

// Commit and management
$junos->commit();
$junos->commitCheck();
$junos->rollback(1);
$junos->showChanges();
$junos->saveRescueConfig();

// Diagnostics
$junos->ping('8.8.8.8');
$junos->monitorInterface('ge-0/0/0', 2);
$junos->getAlarms();
$junos->getActiveUsers();
$junos->execOperational('show chassis hardware');
```

#### 4.4.3 MikrotikOps — MikroTik RouterOS (79 methods)

```php
// System
$mt->getSystemIdentity();
$mt->setSystemIdentity('Core-Router');
$mt->getVersion();
$mt->getResources();
$mt->getHealth();
$mt->getUptime();
$mt->reboot();

// Interfaces
$mt->getInterfaces();
$mt->enableInterface('ether1');
$mt->disableInterface('ether2');
$mt->setInterfaceComment('ether1', 'WAN Link');

// IP and routing
$mt->getIpAddresses();
$mt->addIpAddress('192.168.1.1/24', 'ether1');
$mt->getRoutes();
$mt->addRoute('0.0.0.0/0', '192.168.1.254');
$mt->setDefaultGateway('192.168.1.254');

// VLAN and bridges
$mt->createVlan(10, 'VLAN10', 'ether2');
$mt->removeVlan('VLAN10');
$mt->createBridge('Bridge-LAN');
$mt->addBridgePort('Bridge-LAN', 'ether3');

// Firewall and NAT
$mt->getFirewallRules();
$mt->addFirewallRule('forward', 'drop', 'tcp', '0.0.0.0/0', '10.0.0.0/8');
$mt->addNatRule('srcnat', 'masquerade', '192.168.1.0/24');
$mt->getNatRules();

// User management
$mt->getUsers();
$mt->createUser('jdoe', 'P@ssw0rd', 'full');

// Backup and configuration
$mt->exportConfig();
$mt->saveBackup('pre-upgrade');
$mt->loadBackup('pre-upgrade');

// Diagnostics
$mt->ping('8.8.8.8', 5, 64);
$mt->traceroute('8.8.8.8');
$mt->getLog(50);
$mt->getDhcpLeases();
$mt->getConnections();
$mt->exec('/interface wireless scan wlan1');
```

#### 4.4.4 HuaweiVrpOps — Huawei VRP (64 methods)

```php
// Views and show commands
$huawei->enterSystemView();
$huawei->returnToUserView();
$huawei->getHostname();
$huawei->getVersion();
$huawei->getCurrentConfig();
$huawei->getInterfaces();
$huawei->getVlans();
$huawei->getIpRouteTable();

// VLAN
$huawei->createVlan(10, 'Users');
$huawei->createVlanBatch([10, 20, 30]);
$huawei->deleteVlan(10);
$huawei->setInterfaceAccessVlan('GigabitEthernet0/0/1', 10);
$huawei->setInterfaceTrunk('GigabitEthernet0/0/24', '10 20');
$huawei->setInterfaceHybrid('GigabitEthernet0/0/2', '10', '20 30');

// Interface
$huawei->setInterfaceDescription('GigabitEthernet0/0/1', 'Link to Core');
$huawei->setInterfaceIp('GigabitEthernet0/0/1', '192.168.1.1', '24');
$huawei->createVlanif(10, '192.168.10.1', '24');
$huawei->shutdownInterface('GigabitEthernet0/0/2');

// Routing and ACL
$huawei->addStaticRoute('0.0.0.0', '0.0.0.0', '192.168.1.254');
$huawei->setDefaultGateway('192.168.1.254');
$huawei->addAclRule(3000, 'deny', '10.0.0.0 0.255.255.255');
$huawei->applyAclInbound('GigabitEthernet0/0/1', 3000);
$huawei->enableSsh('P@ssw0rd');

// Services
$huawei->setNtpServer('pool.ntp.org');
$huawei->setSnmpCommunity('public', 'ro');
$huawei->setSyslogServer('192.168.1.100');
$huawei->enableLldp();
$huawei->createEthTrunk(1, ['GigabitEthernet0/0/1', 'GigabitEthernet0/0/2']);

// Diagnostics
$huawei->ping('8.8.8.8');
$huawei->getLldpNeighbors();
$huawei->getOspfNeighbors();
$huawei->getDeviceStatus();
$huawei->saveConfig();
$huawei->exec('display ip interface brief');
```

#### 4.4.5 PaloAltoOps — Palo Alto PAN-OS (56 methods)

```php
// System
$pa->getSystemInfo();
$pa->getVersion();
$pa->getResources();
$pa->setHostname('PA-500');
$pa->setDomainName('example.local');
$pa->setDnsServers(['8.8.8.8', '8.8.4.4']);
$pa->setNtpServer('pool.ntp.org');

// Interfaces and zones
$pa->getInterfaces();
$pa->setInterfaceIp('ethernet1/1', '192.168.1.1/24');
$pa->setInterfaceZone('ethernet1/1', 'trust');
$pa->createZone('dmz', 'layer3', ['ethernet1/2']);

// Routing
$pa->getRoutes();
$pa->addStaticRoute('default-route', '0.0.0.0/0', '192.168.1.254');
$pa->removeStaticRoute('default-route');

// Security rules
$pa->getSecurityRules();
$pa->addSecurityRule('Allow-HTTP', 'allow', 'trust', 'untrust', '10.0.0.0/8', 'any', 'web-browsing', 'tcp-80');
$pa->deleteSecurityRule('Allow-HTTP');
$pa->disableSecurityRule('Old-Rule');

// NAT
$pa->getNatRules();
$pa->addNatRule('SNAT-LAN', 'ipv4', 'trust', 'untrust', '10.0.0.0/8', 'any');

// Objects
$pa->createAddress('web-server', 'ip-netmask', '10.0.1.10/32');
$pa->createService('HTTP', 'tcp', '80');

// Commit
$pa->commit('Added HTTP rule');
$pa->commitForce();
$pa->showChanges();
$pa->discardConfig();

// Diagnostics
$pa->ping('8.8.8.8');
$pa->getSystemLogs(50);
$pa->getTrafficLogs(50);
$pa->getSessions();
$pa->getLicenses();
$pa->exec('show system info');
```

#### 4.4.6 FortinetOps — Fortinet FortiOS (59 methods)

```php
// System
$frt->getSystemStatus();
$frt->getVersion();
$frt->getPerformance();
$frt->getHaStatus();
$frt->setHostname('FortiGate-100D');
$frt->setDnsServers('8.8.8.8', '8.8.4.4');
$frt->setNtpServer('pool.ntp.org');

// Interfaces
$frt->getInterfaces();
$frt->setInterfaceIp('port1', '192.168.1.1', '255.255.255.0', 'ping https');
$frt->setInterfaceDescription('port1', 'WAN Link');
$frt->enableInterface('port2');
$frt->disableInterface('port3');
$frt->createVlan(10, 'port1', 10);

// Routing
$frt->getRoutes();
$frt->addStaticRoute(1, '0.0.0.0/0', '192.168.1.254', 'port1');
$frt->removeStaticRoute(1);
$frt->setDefaultGateway('192.168.1.254', 'port1');

// Firewall policies
$frt->getPolicies();
$frt->addPolicy(1, 'Allow-Web', 'accept', 'port2', 'port1', 'all', 'all', 'HTTP');
$frt->deletePolicy(1);
$frt->enablePolicy(2);
$frt->disablePolicy(3);

// Objects and administration
$frt->createAddress('Web-Server', '10.0.1.10', '255.255.255.255');
$frt->createAddressGroup('Web-Servers', ['Web-Server']);
$frt->createService('Custom-HTTP', 'tcp', '8080');
$frt->createAdmin('jdoe', 'P@ssw0rd', 'super_admin');

// Backup
$frt->backupConfigTftp('192.168.1.100', 'fgt-backup.conf');
$frt->restoreConfigTftp('192.168.1.100', 'fgt-backup.conf');

// Diagnostics
$frt->ping('8.8.8.8');
$frt->getSessions();
$frt->getEventLogs(50);
$frt->getAttackLogs(50);
$frt->getDiagnostics();
$frt->exec('get system performance status');
```

#### 4.4.7 CheckPointOps — Check Point Gaia (43 methods)

```php
// System
$cp->getVersion();
$cp->getHostname();
$cp->setHostname('GW-Core');
$cp->getAssetInfo();
$cp->setNtpServer('pool.ntp.org');
$cp->setDnsServers('8.8.8.8', '8.8.4.4');

// Interfaces
$cp->getInterfaces();
$cp->setInterfaceIp('eth0', '192.168.1.1', 24);
$cp->enableInterface('eth1');
$cp->disableInterface('eth1');
$cp->setInterfaceComment('eth0', 'WAN Link');
$cp->setInterfaceMtu('eth0', 1500);

// Routing
$cp->getRoutes();
$cp->addStaticRoute('0.0.0.0/0', '192.168.1.254');
$cp->removeStaticRoute('10.0.0.0/8');
$cp->setDefaultGateway('192.168.1.254');

// Cluster and firewall
$cp->getClusterStatus();
$cp->getFirewallStatus();
$cp->getGatewayStatus();

// Users and SNMP
$cp->getUsers();
$cp->createUser('jdoe', 'P@ssw0rd');
$cp->addSnmpCommunity('public');
$cp->setSnmpAgent(true);

// Diagnostics
$cp->ping('8.8.8.8', 5, 64);
$cp->traceroute('8.8.8.8');
$cp->getLogs(50);
$cp->getTasks();
$cp->getConfig();
$cp->getLicenses();
$cp->exec('show asset all');
```

All methods return `RemoteCommandOutput`.

### 4.5 BSD (`AbstractBsdOps`)

Reuses `UnixFileTrait` (files), `BsdServiceTrait`, `BsdUserTrait`, `BsdNetworkTrait`.

#### FreeBSD

```php
$freebsd = new FreeBsdOps();
$freebsd->installPackage('nginx');                    // pkg install -y
$freebsd->uninstallPackage('nginx');                  // pkg delete
$freebsd->upgradePackages();                          // pkg upgrade
$freebsd->manageService('nginx', 'start');            // service nginx start
$freebsd->addUser('jdoe', 'P@ssw0rd');                // pw useradd
$freebsd->getFirewallStatus();                        // pfctl -s info
$freebsd->addFirewallRule('block all');               // echo ... | pfctl -f -
```

#### OpenBSD

```php
$openbsd = new OpenBsdOps();
$openbsd->installPackage('nginx');                    // pkg_add
$openbsd->uninstallPackage('nginx');                  // pkg_delete
$openbsd->manageService('httpd', 'status');           // service httpd status
```

### 4.6 Cloud — Azure

```php
$azure = new AzureOps();
$azure->loginWithServicePrincipal($tenantId, $clientId, $clientSecret);
$azure->setSubscription('sub-123');
$azure->createResourceGroup('prod-rg', 'westeurope');
$azure->createVirtualMachine('web-01', 'prod-rg', 'Standard_B2s', '...');
$azure->listVirtualMachines('prod-rg');
```

### 4.7 Cloud — OpenStack

```php
$openstack = new OpenStackOps();
$openstack->setOpenStackAuthentication($authUrl, $project, $user, $password);
$openstack->authenticate();
$openstack->createServer('web-01', 'Ubuntu20.04', 'm1.small', 'private-net', 'ssh-key');
$openstack->listServers();
```

### 4.8 Cloud — GCP

```php
$gcp = new GcpOps();
$gcp->authenticateWithServiceAccountKey('/tmp/sa-key.json');
$gcp->setProject('my-project');
$gcp->createInstance('web-01', 'europe-west1-b', 'e2-medium',
                     'ubuntu-2004-lts', 'ubuntu-os-cloud', 'default');
```

### 4.9 Cloud — AWS

```php
$aws = new AwsOps();
$aws->setProfile('prod');
$aws->setRegion('eu-west-1');
$aws->configureCredentials($accessKey, $secretKey, 'eu-west-1');
$aws->createInstance('web-01', 'ami-1234', 't3.micro', 'subnet-xxx', 'my-key', 'sg-yyy');
```

### 4.10 Virtualization — KVM/libvirt

```php
$kvm = new KvmOps();
$kvm->getVirshVersion();
$kvm->createVm('vm01', '2', '2048', '/var/lib/libvirt/images/vm01.qcow2');
$kvm->startVm('vm01');
$kvm->getVmStatus('vm01');
$kvm->setVmCpu('vm01', '4');
$kvm->setVmMemory('vm01', '8192');
$kvm->listVMs();
$kvm->stopVm('vm01');
```

### 4.11 Virtualization — VirtualBox

```php
$vbox = new VirtualboxOps();
$vbox->getVirtualboxVersion();
$vbox->createVm('vm01', 'Ubuntu_64', '2048', '2');
$vbox->createAndAttachDisk('vm01', '/vhd/disk.vdi', '40000');
$vbox->startVm('vm01', 'headless');
$vbox->listRunningVMs();
$vbox->cloneVm('vm01', 'vm01-clone', 'full');
$vbox->powerOffVm('vm01');
```

### 4.12 Virtualization — Proxmox

```php
$proxmox = new ProxmoxOps();
$proxmox->getProxmoxVersion();                        // pvesh get /version
$proxmox->listNodes();                                // pvesh get /nodes
$proxmox->listVMs();                                  // qm list
$proxmox->createVm('100', 'vm01', '2048', '2', 'local:0');
$proxmox->startVm('100');                             // qm start
$proxmox->getVmStatus('100');                         // qm status
$proxmox->setVmCpu('100', '4');                       // qm set --cores
$proxmox->setVmMemory('100', '8192');                 // qm set --memory
$proxmox->createSnapshot('100', 'snap-pre-upgrade');  // qm snapshot
$proxmox->listSnapshots('100');                       // qm listsnapshot
$proxmox->cloneVm('100', '101', 'vm01-clone');        // qm clone
$proxmox->stopVm('100');                              // qm shutdown
$proxmox->killVm('100');                              // qm stop (force)
$proxmox->deleteVm('100');                            // qm destroy
```

## 5. Detailed Usage and Examples

### 5.1 SSH connection — authentication modes

#### Password

```php
$ops->setCredentials('admin', 'secret');
$ops->openConnection();
```

#### RSA key

```php
$ops->setRsaAuthentication('admin', '/home/user/.ssh/id_rsa', '/home/user/.ssh/id_rsa.pub');
$ops->openConnection();
```

#### SSH agent

```php
$ops->setAgentAuthentication('admin');
$ops->openConnection();   // uses ssh2_auth_agent()
```

### 5.2 Jump host (SSH proxy)

```php
$ops->setHost('db.internal.example.local');
$ops->setProxy('bastion.example.local');              // tunnel via bastion
$ops->openConnection();                               // ssh2_tunnel() to final target
```

### 5.3 Configurable timeout

```php
$ops->setSshTimeout(10);                              // abort after 10s
$ops->openConnection();
$ops->isConnected();                                  // bool
```

### 5.4 SSH config integration

```php
// Parse ~/.ssh/config and apply settings for a host
$config = AbstractOps::parseSshConfig('prod-web');
// Returns: ['hostname' => '10.0.0.prod-web', 'port' => 2222, 'user' => 'admin', ...]

// Apply SSH config settings directly to an instance
$ops->applySshConfig('db-main');
// Sets host, port, user, and proxy from ~/.ssh/config
$ops->openConnection();
```

### 5.5 Host key fingerprint verification

```php
// Enable strict checking (optional — requires a known_hosts file)
$ops->setStrictHostKeyChecking(true);
$ops->setKnownHostsFile('/home/user/.ssh/known_hosts');
$ops->openConnection();

// Retrieve fingerprint (always available after connect)
$fingerprint = $ops->getHostFingerprint();
echo "Server fingerprint: $fingerprint";
```

### 5.6 File transfer

### 5.4 File transfer

```php
// SCP (via ssh2_scp_send / ssh2_scp_recv)
$ops->scpPut('/local/config.yaml', '/remote/config.yaml', '0644');
$ops->scpGet('/remote/backup.sql', '/local/backup.sql');

// SFTP (via ssh2_sftp, streaming)
$ops->sftpPut('/local/data.csv', '/remote/imports/data.csv');
$ops->sftpGet('/remote/logs/app.log', '/local/app.log');

// By command (base64) — for text files
$ops->writeFile('/remote/config.php', "<?php\nreturn ['key'=>'value'];\n");
$content = $ops->readFile('/remote/config.php');
```

### 5.5 Error handling

```php
use Cyonima\Ops\Exception\ConnectionException;
use Cyonima\Ops\Exception\AuthenticationException;
use Cyonima\Ops\Exception\ExecutionException;

try {
    $ops->openConnection();
    $result = $ops->remoteExec('ls /nonexistent');
    $result->throwIfFailed();                         // ExecutionException if exit != 0
} catch (ConnectionException $e) {
    echo 'Connection failed: ' . $e->getMessage();
} catch (AuthenticationException $e) {
    echo 'Authentication failed: ' . $e->getMessage();
} catch (ExecutionException $e) {
    echo 'Command failed: ' . $e->getMessage();
    echo $result->getStderr();                        // Remote error output
}
```

## 6. Security, Validation and Escaping

### 6.1 General principle

All arguments passed to remote commands are escaped via
`escapeShellArgument()` (POSIX shell) or `escapePowerShellArgument()` (PowerShell).
Never concatenate unescaped user data.

### 6.2 Shell injection detection

`InputValidator::validateCommand(string, bool $strict)` detects:

- `; & | \` $ < > ( )` — shell operators
- `${ }` — variable substitution
- `` ` `` — command substitution
- `%0x` — URL-encoded injection

```php
// ✅ Safe
InputValidator::validateCommand('ls -la /tmp', true);   // passes

// ❌ Throws InvalidArgumentException
InputValidator::validateCommand('rm -rf /; echo hacked', true);
```

### 6.3 Secure sudo

The `executeWithSudo()` method never exposes the password in `ps aux`.
It uses a protected temporary file:

```php
// 1. base64_encode(password) → /tmp/._sudo_<random>
// 2. chmod 400
// 3. sudo -S command < tmpfile
// 4. rm -f tmpfile
```

### 6.4 Best practices

- Prefer SSH key authentication over password
- Never commit plain-text passwords (use environment variables
  or a secret manager: Vault, AWS Secrets Manager, etc.)
- For critical operations (VM deletion, firewall changes),
  add human validation or use a staging environment
- Apply the principle of least privilege for remote sudo accounts

## 7. Static Analysis (PHPStan)

The project is configured for PHPStan level 6.

```bash
composer analyze
# or
vendor/bin/phpstan analyse
```

Configuration (`phpstan.neon`) ignores `ssh2_*` functions (extension not
installed in development) and untyped SSH/SFTP resource properties.

## 8. Testing, Coverage and CI

### Running tests

```bash
vendor/bin/phpunit
```

### Writing tests

Tests mock `remoteExec()` via anonymous classes:

```php
final class UbuntuOpsTest extends TestCase
{
    public function testInstallPackageUsesApt(): void
    {
        $ops = new class() extends UbuntuOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->installPackage('nginx');

        $this->assertStringContainsString('apt install -y', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
```

### CI (GitHub Actions)

Workflow: `.github/workflows/phpunit.yml`

- PHP 8.1 / 8.2 / 8.3
- `composer install`
- `vendor/bin/phpunit --coverage-clover=coverage.xml`
- Upload to Codecov

## 9. Troubleshooting and Diagnostics

### SSH

- Check connectivity: `ping <host>`, `nc -zv <host> 22`
- Check SSH server logs (`/var/log/auth.log`)
- Verify `ext-ssh2` is installed: `php -m | grep ssh2`
- Test manually: `ssh -v user@host`

### PowerShell

- Inspect `RemoteCommandOutput::getStderr()` for PowerShell errors
- Run the script manually via SSH: `powershell -Command "..."`

### WinRM

- Run `winrm quickconfig` on the target machine
- Check listeners: `winrm enumerate winrm/config/Listener`
- Check firewall (ports 5985 HTTP, 5986 HTTPS)

## 10. Contributing and Publishing

- Fork and PR on https://github.com/LudovicBoudi/Cyonima.ops.php.lib
- Standards: PSR-12, strict typing (`declare(strict_types=1)`)
- Tests: PHPUnit, all new features must be tested
- Static analysis: `composer analyze` (PHPStan level 6, 0 errors)
- Versioning: SemVer, update CHANGELOG.md, create a Git tag

## 11. Appendices

### 11.1 Exceptions

| Exception | Thrown |
|-----------|--------|
| `Cyonima\Ops\Exception\ConnectionException` | Network failure, missing ssh2 extension |
| `Cyonima\Ops\Exception\AuthenticationException` | Auth failure (password, key, agent) |
| `Cyonima\Ops\Exception\ExecutionException` | Remote command returns exit ≠ 0 |

### 11.2 Important files

```
src/Cyonima/Ops/AbstractOps.php           # Base SSH/SCP/SFTP class
src/Cyonima/Ops/RemoteCommandOutput.php   # Return value
src/Cyonima/Ops/InputValidator.php        # Validation
src/Cyonima/Ops/Traits/UnixFileTrait.php  # File operations (Linux+BSD)
src/Cyonima/Ops/Linux/AbstractLinuxOps.php
src/Cyonima/Ops/Bsd/AbstractBsdOps.php
src/Cyonima/Ops/Windows/AbstractWindowsOps.php
src/Cyonima/Ops/Windows/WinRmClient.php
phpstan.neon                               # Static analysis config
phpunit.xml                                # Test config
```

### 11.3 Constants

```php
AbstractOps::DEFAULT_SSH_PORT = 22
AbstractOps::DEFAULT_SSH_TIMEOUT = 30
```

---

*End of detailed manual.*
