# Cyonima OPS PHP Library

[![PHPUnit](https://img.shields.io/badge/CI-GitHub%20Actions-blue.svg)](https://github.com/LudovicBoudi/Cyonima.ops.php.lib/actions/workflows/phpunit.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%206-brightgreen.svg)](https://phpstan.org/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A modern PHP 8.1+ library for infrastructure automation via SSH/SCP/SFTP and WinRM.
Manage Linux, BSD, Windows, macOS, Cisco, Cloud (AWS/Azure/GCP/OpenStack), and
virtualization platforms (KVM, VirtualBox, Proxmox) — all through typed, testable
PHP helpers.

## Features

✅ **SSH/SCP/SFTP Operations**
- Password, RSA key, and SSH agent authentication
- Direct connections and proxy (jump host) support
- Remote command execution with full output capture (`RemoteCommandOutput`)
- Configurable connection timeout
- Host key fingerprint verification (`getHostFingerprint`)
- Strict host key checking against known_hosts file
- `~/.ssh/config` parsing and integration (`parseSshConfig` / `applySshConfig`)
- File transfer via SCP (`scpPut`/`scpGet`)
- File transfer via SFTP (`sftpPut`/`sftpGet`)
- Secure sudo execution (password never exposed in process list)

✅ **Linux System Management**
- Distributions: Ubuntu, Linux Mint, Zorin (apt); Red Hat, CentOS, Rocky, Fedora (yum/dnf); SUSE (zypper)
- User accounts: add, delete, change password (with/without sudo)
- Services: systemctl (status, start, stop, restart, enable, disable)
- Packages: install, uninstall, upgrade (with/without sudo)
- Files: read, write (base64), copy, move, remove, mkdir, chmod, chown
- Processes: list, kill by PID or name
- Logs: tail files, journalctl
- Network: interfaces, routing table, add routes
- Firewall: ufw/firewall-cmd (status, enable, disable, add rules)
- SELinux: status, set mode
- AppArmor: status, enforce profile

✅ **BSD System Management**
- FreeBSD (pkg) and OpenBSD (pkg_add/pkg_delete)
- User, service, firewall (pfctl), and file management
- Shared `UnixFileTrait` with Linux

✅ **Windows System Management**
- PowerShell over SSH (`WindowsOps`)
- Native WinRM (`WinRmClient` + `WindowsWinRmOps`)
- Services, users, files, processes, event logs, firewall
- Active Directory: join domain, create AD users, add to groups
- IIS, SQL Server, Hyper-V management modules

✅ **macOS System Management**
- Homebrew packages (`brew install`/`uninstall`/`upgrade`)
- Services via `brew services`
- Users via `sysadminctl`

✅ **Containers & Orchestration**
- Docker: containers, images, compose, logs, networks, volumes
- Kubernetes: pods, services, deployments, helm charts, kubeconfig

✅ **Database Management**
- MySQL: queries, dump/restore, users, grants, databases
- PostgreSQL: queries, dump/restore, users, grants, databases

✅ **Web Servers**
- Nginx: vhosts, sites, logs, config test, reload
- Apache: vhosts, a2ensite/a2dissite, logs, config test

✅ **System Administration**
- System inventory: CPU, memory, disk, OS, processes, network
- Cron jobs: list, add, remove, backup, restore
- SSH keys: authorized_keys management, key generation

✅ **Load Balancing**
- HAProxy: stats, backend/server management, config validation

✅ **SSL/TLS Certificates**
- Certbot/Let's Encrypt: obtain, renew, revoke, list certificates

✅ **Backup & Sync**
- Rsync: remote sync, incremental backups, archive/extract

✅ **Network Equipment**
- Cisco IOS: running config, interfaces, VLAN, ACL, routes, NetFlow

✅ **Cloud Providers**
- AWS, Azure, GCP, OpenStack — CLI execution via SSH

✅ **Virtualization**
- KVM/libvirt (virsh), VirtualBox (VBoxManage), Proxmox VE (qm + pvesh), VMware/ESXi (vim-cmd + esxcli)

✅ **Modern PHP**
- PHP 8.1+ (typed properties, readonly, Stringable, enums)
- PSR-4 autoloading, PSR-3 logging, PSR-12 coding style
- PHPStan level 6 — 0 errors
- PHPUnit 10 — 94 tests, 223 assertions, 0 failures

## Installation

```bash
composer require cyonima/ops-lib
```

### Requirements

- PHP 8.1+
- `ext-ssh2` — SSH/SCP/SFTP connections
- `ext-curl` — WinRM support
- `ext-mbstring` (suggested) — PowerShell encoding

## Quick Start

### SSH connection and command

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setHost('192.168.1.100')
    ->setCredentials('ubuntu', 'password')
    ->setSshPort(22);

try {
    $ops->openConnection();
    $output = $ops->remoteExec('uname -a');
    echo $output->getStdout();
    $ops->closeConnection();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### RSA key / Agent authentication

```php
// RSA key
$ops->setRsaAuthentication('ubuntu', '/home/user/.ssh/id_rsa', '/home/user/.ssh/id_rsa.pub');

// SSH agent (no key file needed — uses running ssh-agent)
$ops->setAgentAuthentication('ubuntu');

// Priority: agent → RSA → password
```

### File transfer (SCP & SFTP)

```php
$ops->scpPut('/local/file.txt', '/remote/file.txt', '0644');
$ops->scpGet('/remote/backup.sql', '/local/backup.sql');

$ops->sftpPut('/local/data.csv', '/remote/imports/data.csv');
$ops->sftpGet('/remote/logs/app.log', '/local/app.log');
```

### RemoteCommandOutput

```php
$result = $ops->remoteExec('ls /nonexistent');
if ($result->isSuccessful()) {
    echo $result->getStdout();
} else {
    echo "Failed (exit {$result->getExitCode()}): {$result->getStderr()}";
}
$result->throwIfFailed();  // throws ExecutionException
```

### Proxy / Jump host

The SSH session connects to the jump host; commands are then relayed to the
final target by invoking `ssh` on the jump host.

```php
$ops->setProxy('10.0.0.1')        // Jump host
    ->setProxyTargetPort(22)      // Target SSH port (optional, default 22)
    ->setHost('192.168.1.100')    // Final target
    ->setCredentials('user', 'password')
    ->openConnection();

$ops->remoteExec('uname -a');     // Runs on 192.168.1.100 through the jump host
```

Notes on jump host mode:
- With password auth, the jump host must have `sshpass` installed (the password
  is passed via a `chmod 400` temp file, never on the command line).
- With RSA key / agent auth, the jump host is expected to have its own key-based
  access to the target (`BatchMode` is enabled to avoid interactive prompts).
- SCP/SFTP file transfers are **not** supported in jump host mode. Connect
  directly to the target to transfer files.

### SSH config integration

```php
// Auto-configure from ~/.ssh/config
$ops->applySshConfig('prod-web');
$ops->openConnection();           // Uses HostName, Port, User, ProxyJump from config

// Or parse config manually
$config = AbstractOps::parseSshConfig('db-main');
// $config['hostname'], $config['port'], $config['user'], etc.
```

### Host key fingerprint

```php
$ops->openConnection();
$fingerprint = $ops->getHostFingerprint();     // SHA-1 hex fingerprint
$ops->setStrictHostKeyChecking(true);          // Verify against known_hosts
```

## Class Hierarchy

```
Cyonima\Ops\
├── AbstractOps                    # Base: SSH, SCP, SFTP, auth
│   ├── setHost() / setSshPort() / setSshTimeout()
│   ├── setCredentials() / setRsaAuthentication() / setAgentAuthentication()
│   ├── setProxy() / unsetProxy()
│   ├── setStrictHostKeyChecking() / setKnownHostsFile() / getHostFingerprint()
│   ├── applySshConfig() / parseSshConfig() (static)
│   ├── openConnection() / closeConnection() / isConnected()
│   ├── remoteExec() → RemoteCommandOutput
│   ├── scpPut() / scpGet() / sftpPut() / sftpGet()
│   └── setLogger()
│
├── RemoteCommandOutput            # stdout, stderr, exitCode, isSuccessful()
├── InputValidator                 # validateHost, validateCommand, etc.
│
├── Traits\
│   ├── UnixFileTrait              # readFile, writeFile, copy, move, remove, mkdir, chmod, chown
│   ├── Linux\
│   │   ├── LinuxServiceTrait      # systemctl (status, start, stop, restart, enable, disable)
│   │   ├── LinuxUserTrait         # addUser, deleteUser, changePassword
│   │   └── LinuxSystemTrait       # processes, logs, network, firewall, SELinux, AppArmor
│   └── Bsd\
│       ├── BsdServiceTrait        # getService, manageService
│       ├── BsdUserTrait           # addUser, deleteUser, changePassword
│       └── BsdNetworkTrait        # getFirewallStatus, addFirewallRule
│
├── Linux\
│   ├── AbstractLinuxOps (uses 4 traits)   # abstract package mgmt
│   ├── UbuntuOps                          # apt
│   ├── RedhatOps                          # yum/dnf
│   ├── SuseOps                            # zypper
│   ├── LinuxMintOps                       # apt (Ubuntu derivative)
│   └── ZorinOps                           # apt (Ubuntu derivative)
│
├── Bsd\
│   ├── AbstractBsdOps (uses 4 traits)     # abstract package mgmt
│   ├── FreeBsdOps                         # pkg
│   └── OpenBsdOps                         # pkg_add/pkg_delete
│
├── Windows\
│   ├── AbstractWindowsOps / WindowsOps    # PowerShell over SSH
│   ├── WindowsWinRmOps / WinRmClient      # Native WinRM (SOAP/cURL)
│   ├── IISOps / SQLServerOps / HyperVOps
│
├── MacOS\ MacOsOps                        # brew, sysadminctl
├── Network\
│   ├── CiscoOps                           # IOS CLI
│   └── JuniperOps                         # JunOS CLI
├── Docker\ DockerOps                      # containers, compose
├── Database\
│   ├── AbstractDatabaseOps
│   ├── MySqlOps                           # mysql, mysqldump
│   └── PostgreSqlOps                      # psql, pg_dump
├── WebServer\
│   ├── AbstractWebServerOps
│   ├── NginxOps                           # nginx, sites, vhosts
│   └── ApacheOps                          # apache2ctl, a2ensite
├── System\
│   ├── SystemOps                          # inventory, facts
│   ├── CronOps                            # crontab management
│   └── SshKeyOps                          # authorized_keys
├── Kubernetes\ KubernetesOps              # kubectl, helm
├── LoadBalancer\ HaProxyOps               # HAProxy stats, servers
├── Ssl\ CertbotOps                        # Let's Encrypt SSL
├── Backup\ RsyncOps                       # rsync, archives
├── Vmware\ VmwareOps                      # vim-cmd, esxcli
├── Aws\ AwsOps / Azure\ AzureOps
├── Gcp\ GcpOps / OpenStack\ OpenStackOps
├── Kvm\ KvmOps                            # virsh
├── Virtualbox\ VirtualboxOps              # VBoxManage
└── Proxmox\ ProxmoxOps                    # qm + pvesh
```

## Linux Distribution Support

| Distribution | Manager | Class | Status |
|-------------|---------|-------|--------|
| Ubuntu | apt | `UbuntuOps` | ✅ |
| Debian | apt | `DebianOps` | ✅ |
| Linux Mint | apt | `LinuxMintOps` | ✅ |
| Zorin OS | apt | `ZorinOps` | ✅ |
| Red Hat / RHEL | yum | `RedhatOps` | ✅ |
| CentOS | yum/dnf | `CentosOps` | ✅ |
| Rocky Linux | dnf | `RockyOps` | ✅ |
| Fedora | dnf | `FedoraOps` | ✅ |
| SUSE/openSUSE | zypper | `SuseOps` | ✅ |

## Security

- **All arguments are escaped** via `escapeShellArgument()` (POSIX) or
  `escapePowerShellArgument()` (PowerShell) before remote execution
- **`executeWithSudo()`** uses a temp file (`chmod 400`) + `base64` + `rm -f`
  instead of `echo password | sudo -S` — never visible in `ps aux`
- **`InputValidator::validateCommand()`** detects shell injection characters
  (`; & | \` $ < > ( ) ${ })
- **SSH agent authentication** is preferred over RSA keys and passwords
- **PSR-3 logging** for auditing all operations

## Documentation

- **[manual-fr.md](manual-fr.md)** — Full manual in French
- **[manual-En.md](manual-En.md)** — Full manual in English

Both cover installation, architecture, all API methods, security, PHPStan,
testing, and troubleshooting.

## Development

```bash
# Run tests
composer test                  # or vendor/bin/phpunit

# Static analysis (level 6)
composer analyze               # or vendor/bin/phpstan analyse

# Code style (PSR-12)
composer fix                   # or vendor/bin/php-cs-fixer fix src/ --rules=@PSR12
```

## License

MIT — see [LICENSE](LICENSE).

## Support

Open an issue at: https://github.com/LudovicBoudi/Cyonima.ops.php.lib
