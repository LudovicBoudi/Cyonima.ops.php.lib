# Complete Manual — Cyonima OPS PHP Library

Last updated: June 1, 2026


This manual is comprehensive documentation for the Cyonima OPS library. It provides
in-depth information for installation, configuration, architecture understanding,
API usage, operational examples, security best practices, test execution, and
continuous integration.

Table of Contents
- 1. Overview
- 2. Installation and Prerequisites
- 3. Architecture and Key Concepts
  - 3.1 `AbstractOps`
  - 3.2 `RemoteCommandOutput`
  - 3.3 `InputValidator` & Escaping
  - 3.4 Logging (PSR-3)
- 4. Modules and Detailed APIs
  - 4.1 Linux (AbstractLinuxOps, UbuntuOps, derivatives)
  - 4.2 Windows (AbstractWindowsOps, WindowsOps, WinRmClient)
  - 4.3 macOS (AbstractMacOsOps, MacOsOps)
  - 4.4 Network / CiscoOps
  - 4.5 BSD (AbstractBsdOps, FreeBsdOps, OpenBsdOps)
  - 4.6 Azure (AbstractAzureOps, AzureOps)
  - 4.7 OpenStack (AbstractOpenStackOps, OpenStackOps)
  - 4.8 GCP (AbstractGcpOps, GcpOps)
  - 4.9 AWS (AbstractAwsOps, AwsOps)
  - 4.10 KVM/libvirt (AbstractKvmOps, KvmOps)
  - 4.11 VirtualBox (AbstractVirtualboxOps, VirtualboxOps)
  - 4.12 Proxmox (AbstractProxmoxOps, ProxmoxOps)
- 5. Detailed Usage and Examples (with snippets)
  - 5.1 SSH Connection and Execution
  - 5.2 File Transfer (SCP)
  - 5.3 Windows: PowerShell over SSH & WinRM
  - 5.4 AD / IIS / SQL Server / Hyper-V Examples
  - 5.5 Linux Mint & Zorin Usage
  - 5.6 FreeBSD / OpenBSD Examples
  - 5.7 Azure CLI Examples
  - 5.8 OpenStack CLI Examples
  - 5.9 GCP CLI Examples
  - 5.10 AWS CLI Examples
  - 5.11 KVM/libvirt Examples
  - 5.12 VirtualBox Examples
  - 5.13 Proxmox Examples
- 6. Security, Validation and Escaping
- 7. Testing, Coverage and CI (GitHub Actions)
- 8. Troubleshooting and Diagnostics
- 9. Contributing and Publishing
- 10. Appendices (Exceptions, Important File Names)

---

1. Overview
-----------

Cyonima OPS is a high-level PHP library designed for infrastructure administration
automation via SSH/SCP and WinRM. It encapsulates recurring operations (package
management, users, services, firewall, network, etc.) behind typed and testable helpers.

The library prioritizes:
- Clear and typed API usage (PHP 8+)
- Systematic argument escaping
- Separation of concerns (ops by OS / module)
- PSR-3 logging compatibility

2. Installation and Prerequisites
---------------------------------

Requirements (development/CI):
- PHP 8.0 or higher
- Composer (for dependencies and test execution)
- Recommended extensions: `ssh2`, `curl`, `xdebug` (for coverage)

Installation:

```bash
composer require cyonima/ops-lib
```

Install development dependencies:

```bash
composer install --no-interaction
```

Configure CI: see `.github/workflows/phpunit.yml` (runs PHPUnit and sends
coverage to Codecov).

3. Architecture and Key Concepts
--------------------------------

3.1 `AbstractOps`
- Entry point for common configuration (host, port, credentials, proxy).
- Essential methods:
  - `setHost(string): self`, `setSshPort(int): self`
  - `setCredentials(string, string): self`
  - `setRsaAuthentication(string, string, string): self`
  - `openConnection(): void`, `closeConnection(): void`
  - `remoteExec(string): RemoteCommandOutput` — remote execution

SSH connection uses `ext-ssh2` when available. The API tries not to return raw
strings: `RemoteCommandOutput` encapsulates output and exit code.

3.2 `RemoteCommandOutput`
- Immutable structure containing: `stdout`, `stderr`, `exitCode`.
- Utility methods:
  - `isSuccessful(): bool` (exit 0)
  - `getStdout(): string`, `getStderr(): string`, `getExitCode(): int`
  - `throwIfFailed(): void` — throws `ExecutionException` on failure

3.3 `InputValidator` & Escaping
- `InputValidator` provides checks (host, port, user, paths, password length, etc.).
- Escaping functions:
  - `escapeShellArgument($s)` — for Linux/macOS shell commands
  - `escapePowerShellArgument($s)` — for PowerShell (used by Windows helpers)

Always validate before calling helpers, especially if user data may be involved.

3.4 Logging (PSR-3)
- `SimpleLogger` implements PSR-3 and is injected by default.
- You can pass any PSR-3 logger via `setLogger(Psr\Log\LoggerInterface $logger)`.

4. Modules and Detailed APIs
----------------------------

4.1 Linux

- `AbstractLinuxOps` exposes:
  - File management: `readFile`, `writeFile`, `copyFileWithPrivilege`, `moveFile`, `removeFile`
  - Service management: `startService`, `stopService`, `enableService`, `restartService`
  - Packages: abstract interface `installPackage`, implementations `UbuntuOps` (apt),
    `RedhatOps` (yum/dnf), `SuseOps` (zypper), etc.
  - Processes: `listProcesses`, `killProcess`
  - Network & firewall: `getNetworkInterfaces`, `addNetworkRoute`, `getFirewallStatus`, etc.

`UbuntuOps` uses `apt` and also provides `WithPrivilege` variants that use `sudo`
or `executeWithSudo()` as appropriate.

4.2 Windows

- `AbstractWindowsOps` and `WindowsOps` (PowerShell over SSH):
  - Secure PowerShell execution via `toPowerShellCommand()` and `escapePowerShellArgument()`
  - Helpers: `readFile`, `writeFile` (uses base64 for transferring contents),
    `getProcesses`, `killProcessById`, `getEventLog`, `getNetworkInterfaces`, `addFirewallRule`
- `WinRmClient` + `WindowsWinRmOps`: Native WinRM access via SOAP/cURL (for non-SSH environments)
- Added modules: `IISOps`, `SQLServerOps`, `HyperVOps`, and `ExchangeOps`, `SharePointOps` skeletons
- AD helpers: `joinDomain(domain, user, password, ou)`, `createAdUser`, `addAdUserToGroup`

4.3 macOS

- `MacOsOps` reuses similar helpers to Linux but adapted: `brew` for packages,
  `brew services` for services, `sysadminctl` for users

4.4 Network / CiscoOps

- `CiscoOps` (in `Network`) provides CLI-oriented network helpers (VLAN configuration,
  interface assignment, routes). These helpers encapsulate network console specifics
  (delays, pagination, prompts)

4.5 BSD

- `AbstractBsdOps` exposes generic BSD helpers for:
  - File management: `readFile`, `writeFile`, `copyFileWithPrivilege`, `removeFile`
  - Service and user management via native BSD tools
  - Package management: `FreeBsdOps` uses `pkg`, `OpenBsdOps` uses `pkg_add`/`pkg_delete`
  - Network and routes on BSD systems
  - Shell command execution and POSIX escaping
- `FreeBsdOps` and `OpenBsdOps` reuse SSH/command wrappers from core `AbstractOps`
  and adapt package management to each distribution's conventions

4.6 Azure

- `AbstractAzureOps` provides Azure CLI execution layer via SSH
- Included helpers: `getAzureCliVersion`, `loginWithServicePrincipal`, `logout`,
  `listSubscriptions`, `setSubscription`, `listResourceGroups`,
  `createResourceGroup`, `deleteResourceGroup`, `listVirtualMachines`,
  `createVirtualMachine`, `startVirtualMachine`, `stopVirtualMachine`,
  `deleteVirtualMachine`
- `AzureOps` is the concrete class for direct usage
- These helpers assume Azure CLI is installed and configured on the remote machine
  and accessible via PATH

4.7 OpenStack

- `AbstractOpenStackOps` provides OpenStack CLI execution layer via SSH
- Included helpers: `setOpenStackAuthentication`, `authenticate`, `getOpenStackVersion`,
  `listProjects`, `listFlavors`, `listImages`, `listNetworks`, `listServers`,
  `createServer`, `startServer`, `stopServer`, `deleteServer`
- `OpenStackOps` is the concrete class for direct usage
- These helpers assume OpenStack client is installed and available in remote PATH,
  and an OpenStack authentication endpoint is accessible

4.8 GCP

- `AbstractGcpOps` provides gcloud CLI execution layer via SSH
- Included helpers: `getGcloudVersion`, `authenticateWithServiceAccountKey`,
  `listProjects`, `setProject`, `applyProject`, `listRegions`, `listZones`,
  `listInstances`, `createInstance`, `startInstance`, `stopInstance`,
  `deleteInstance`
- `GcpOps` is the concrete class for direct usage
- These helpers assume Google Cloud SDK is installed and accessible on the
  remote machine

4.9 AWS

- `AbstractAwsOps` provides AWS CLI execution layer via SSH
- Included helpers: `setProfile`, `setRegion`, `configureCredentials`,
  `getAwsCliVersion`, `listRegions`, `listZones`, `listInstances`,
  `createInstance`, `startInstance`, `stopInstance`, `terminateInstance`
- `AwsOps` is the concrete class for direct usage
- These helpers assume AWS CLI is installed and accessible on the
  remote machine

4.10 KVM/libvirt

- `AbstractKvmOps` provides virsh CLI execution layer via SSH
- Included helpers: `getVirshVersion`, `listVMs`, `getVmStatus`, `getVmInfo`,
  `startVm`, `stopVm`, `destroyVm`, `deleteVm`, `createVm`, `setVmCpu`,
  `setVmMemory`, `listNetworks`, `listStoragePools`
- `KvmOps` is the concrete class for direct usage
- These helpers assume libvirt and virsh are installed and accessible on the
  remote machine

4.11 VirtualBox

- `AbstractVirtualboxOps` provides VBoxManage CLI execution layer via SSH
- Included helpers: `getVirtualboxVersion`, `listVMs`, `listRunningVMs`, `getVmStatus`,
  `getVmInfo`, `startVm`, `stopVm`, `powerOffVm`, `deleteVm`, `createVm`,
  `setVmCpu`, `setVmMemory`, `createAndAttachDisk`, `listNetworks`,
  `cloneVm`
- `VirtualboxOps` is the concrete class for direct usage
- These helpers assume VirtualBox and VBoxManage are installed and accessible
  on the remote machine

4.12 Proxmox

- `AbstractProxmoxOps` provides Proxmox qm and pvesh CLI execution layer via SSH
- Included helpers: `getProxmoxVersion`, `listNodes`, `listVMs`, `getVmStatus`,
  `getVmConfig`, `startVm`, `stopVm`, `killVm`, `deleteVm`, `createVm`,
  `setVmCpu`, `setVmMemory`, `resizeDisk`, `migrateVm`, `listStorages`,
  `getClusterStatus`, `cloneVm`, `createSnapshot`, `listSnapshots`,
  `rollbackSnapshot`
- `ProxmoxOps` is the concrete class for direct usage
- These helpers assume Proxmox VE is installed with access to qm and
  pvesh tools on the remote machine

5. Detailed Usage and Examples
-----------------------------

5.1 SSH Connection and Execution

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setHost('192.0.2.10')->setCredentials('ubuntu','secret')->setSshPort(22);
try {
    $ops->openConnection();
    $out = $ops->remoteExec('uname -a');
    echo $out->getStdout();
    $ops->closeConnection();
} catch (Exception $e) {
    // Handle ConnectionException / AuthenticationException / ExecutionException
}
```

5.2 File Transfer (SCP)

`AbstractOps` provides `scpPut()` / `scpGet()` which rely on `ssh2_scp_send`/
`ssh2_scp_recv` if the extension is available, otherwise it is recommended to use
external `rsync`/`sftp`.

Example:

```php
$ops->scpPut('/local/path/file.txt', '/remote/path/file.txt', '0644');
```

5.3 Windows: PowerShell over SSH & WinRM

- PowerShell over SSH: `WindowsOps::remoteExec($command)` expects a string
  `toPowerShellCommand($psScript)` which wraps the script for compatibility
- For binary files, `writeFile()` encodes to base64 client-side and decodes
  on the target to avoid encoding and escaping issues
- WinRM: `WinRmClient` builds and sends SOAP requests; it exposes high-level
  methods via `WindowsWinRmOps`

Example WinRM (skeleton):

```php
use Cyonima\Ops\Windows\WinRmClient;
$client = new WinRmClient('windows.example.local', 'Administrator', 'password');
$win = new WindowsWinRmOps($client);
$output = $win->getWindowsVersion();
```

5.4 AD / IIS / SQL Server / Hyper-V Examples

AD — joining a domain:

```php
$win->joinDomain('corp.example.local', 'corp\\admin', 'AdminP@ss');
```

IIS — installing and creating a site:

```php
use Cyonima\Ops\Windows\IISOps;
$iis = new IISOps();
$iis->installIIS();
$iis->createWebsite('Site', 'C:\\inetpub\\wwwroot\\site', 8080);
```

SQL Server — running a query:

```php
use Cyonima\Ops\Windows\SQLServerOps;
$sql = new SQLServerOps();
$sql->runQuery('localhost\\SQLEXPRESS', 'master', 'SELECT name FROM sys.databases');
```

Hyper-V — creating a VM:

```php
use Cyonima\Ops\Windows\HyperVOps;
$hv = new HyperVOps();
$hv->createVM('vm1', 2048, 'C:\\vhd\\vm1.vhdx');
```

5.5 Linux Mint & Zorin

These distributions inherit from Ubuntu — use `LinuxMintOps` or `ZorinOps` like
`UbuntuOps`. Differences are mainly pre-installed packages and some paths, but
`apt` commands are identical.

5.6 FreeBSD / OpenBSD

Use `FreeBsdOps` and `OpenBsdOps` to manage BSD hosts via SSH.
These packages reuse `AbstractBsdOps` and provide adapted package management commands:

- FreeBSD: `pkg install`, `pkg delete`, `pkg update`, `pkg upgrade`
- OpenBSD: `pkg_add`, `pkg_delete`, `pkg_info`

Example usage:

```php
use Cyonima\Ops\Bsd\FreeBsdOps;
use Cyonima\Ops\Bsd\OpenBsdOps;

$freebsd = new FreeBsdOps();
$freebsd->setHost('freebsd.example.local')->setCredentials('root', 'password')->setSshPort(22);
$freebsd->openConnection();
$freebsd->installPackage('nginx');
$freebsd->closeConnection();

$openbsd = new OpenBsdOps();
$openbsd->setHost('openbsd.example.local')->setCredentials('root', 'password')->setSshPort(22);
$openbsd->openConnection();
$openbsd->installPackage('nginx');
$openbsd->closeConnection();
```

5.7 Azure CLI Examples

To use Azure via SSH, install Azure CLI on the remote system and use `AzureOps`
to manage Azure resources.

```php
use Cyonima\Ops\Azure\AzureOps;

$azure = new AzureOps();
$azure->setHost('azure-host.example.local')->setCredentials('ops','password')->setSshPort(22);
$azure->openConnection();
$azure->getAzureCliVersion();
$azure->loginWithServicePrincipal('TENANT_ID', 'CLIENT_ID', 'CLIENT_SECRET');
$azure->setSubscription('YOUR_SUBSCRIPTION_ID');
$azure->createResourceGroup('project-rg', 'westeurope');
$azure->listVirtualMachines('project-rg');
$azure->closeConnection();
```

5.8 OpenStack CLI Examples

To use OpenStack via SSH, install the `openstack` client on the remote machine
and use `OpenStackOps` to manage OpenStack resources.

```php
use Cyonima\Ops\OpenStack\OpenStackOps;

$openstack = new OpenStackOps();
$openstack->setHost('openstack-host.example.local')->setCredentials('ops','password')->setSshPort(22);
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

5.9 GCP CLI Examples

To use GCP via SSH, install the `gcloud` client on the remote machine
and use `GcpOps` to manage Google Cloud resources.

```php
use Cyonima\Ops\Gcp\GcpOps;

$gcp = new GcpOps();
$gcp->setHost('gcp-host.example.local')->setCredentials('ops','password')->setSshPort(22);
$gcp->openConnection();
$gcp->authenticateWithServiceAccountKey('/tmp/service-account.json');
$gcp->setProject('my-gcp-project');
$gcp->applyProject();
$gcp->listInstances('europe-west1-b');
$gcp->createInstance('my-instance', 'europe-west1-b', 'e2-medium', 'ubuntu-2004-lts', 'ubuntu-os-cloud', 'default');
$gcp->closeConnection();
```

5.10 AWS CLI Examples

To use AWS via SSH, install the `aws` client on the remote machine
and use `AwsOps` to manage AWS resources.

```php
use Cyonima\Ops\Aws\AwsOps;

$aws = new AwsOps();
$aws->setHost('aws-host.example.local')->setCredentials('ops','password')->setSshPort(22);
$aws->setProfile('default');
$aws->setRegion('eu-west-1');
$aws->openConnection();
$aws->configureCredentials('AKIAEXAMPLE', 'secret-key', 'eu-west-1');
$aws->listInstances('eu-west-1a');
$aws->createInstance('my-instance', 'ami-12345678', 't3.micro', 'subnet-01234567', 'my-key', 'sg-01234567');
$aws->closeConnection();
```

5.11 KVM/libvirt Examples

To use KVM via SSH, ensure libvirt and virsh are installed on the remote machine,
then use `KvmOps` to manage virtual machines.

```php
use Cyonima\Ops\Kvm\KvmOps;

$kvm = new KvmOps();
$kvm->setHost('kvm-host.example.local')->setCredentials('root','password')->setSshPort(22);
$kvm->openConnection();
$kvm->getVirshVersion();
$kvm->listVMs();
$kvm->createVm('my-vm', '2', '2048', '/var/lib/libvirt/images/my-vm.qcow2');
$kvm->startVm('my-vm');
$kvm->getVmStatus('my-vm');
$kvm->setVmCpu('my-vm', '4');
$kvm->setVmMemory('my-vm', '4096');
$kvm->stopVm('my-vm');
$kvm->closeConnection();
```

5.12 VirtualBox Examples

To use VirtualBox via SSH, ensure VirtualBox and VBoxManage are installed on the
remote machine, then use `VirtualboxOps` to manage virtual machines.

```php
use Cyonima\Ops\Virtualbox\VirtualboxOps;

$vbox = new VirtualboxOps();
$vbox->setHost('vbox-host.example.local')->setCredentials('vboxuser','password')->setSshPort(22);
$vbox->openConnection();
$vbox->getVirtualboxVersion();
$vbox->listVMs();
$vbox->listRunningVMs();
$vbox->createVm('test-vm', 'Ubuntu_64', '2048', '2');
$vbox->createAndAttachDisk('test-vm', '/var/vbox/test-vm.vdi', '40000');
$vbox->startVm('test-vm', 'headless');
$vbox->getVmStatus('test-vm');
$vbox->setVmCpu('test-vm', '4');
$vbox->setVmMemory('test-vm', '4096');
$vbox->stopVm('test-vm');
$vbox->closeConnection();
```

5.13 Proxmox Examples

To use Proxmox via SSH, install Proxmox tools (qm and pvesh) on the remote machine,
then use `ProxmoxOps` to manage resources.

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
$proxmox->getVmStatus('100');
$proxmox->setVmCpu('100', '4');
$proxmox->setVmMemory('100', '4096');
$proxmox->createSnapshot('100', 'backup-2026-06-01');
$proxmox->listSnapshots('100');
$proxmox->stopVm('100');
$proxmox->closeConnection();
```

6. Security, Validation and Escaping
------------------------------------

- Always use `InputValidator` to validate: hosts, ports, paths, usernames,
  and allowed commands
- For PowerShell, use `escapePowerShellArgument()`; for POSIX shell,
  `escapeShellArgument()`
- Prefer RSA keys for SSH and restrict permissions (`chmod 600` on
  private keys)
- Never store passwords in plain text in the repository; prefer
  environment variables or a secrets manager (Vault, AWS Secrets)
- For critical operations, require human approval or execute on
  staging environment before production

7. Testing, Coverage and CI
---------------------------

- Tests: PHPUnit. Unit tests are mainly command generation tests and
  wrappers (`tests/*.php`)
- CI workflow: `.github/workflows/phpunit.yml` runs tests on PHP 8.0/8.1/8.2,
  generates `coverage.xml` and sends it to Codecov

Tips for local CI:

```bash
composer install
vendor/bin/phpunit --configuration phpunit.xml --coverage-clover=coverage.xml
```

8. Troubleshooting and Diagnostics
---------------------------------

- SSH connection issues:
  - Check reachability (`ping`, `nc -zv host port`), SSH server logs,
    and that `ext-ssh2` is installed if using PHP bindings
- PowerShell errors: inspect `RemoteCommandOutput::getStderr()` and
  run the script manually via OpenSSH on the target machine
- WinRM: check `winrm quickconfig` and listeners (5985/5986), certificates
  and firewall

9. Contributing and Publishing
------------------------------

- Fork and open a Pull Request. Follow PSR-12 and provide tests
- Versioning: use SemVer. Update `CHANGELOG.md` and create a Git tag for releases

10. Appendices
--------------

Main exceptions:
- `ConnectionException` — network/SSH failure
- `AuthenticationException` — authentication failure
- `ExecutionException` — remote command returns non-zero exit code

Important files:
- `src/Cyonima/Ops/AbstractOps.php`
- `src/Cyonima/Ops/RemoteCommandOutput.php`
- `src/Cyonima/Ops/InputValidator.php`
- `src/Cyonima/Ops/Windows/AbstractWindowsOps.php`
- `src/Cyonima/Ops/Linux/UbuntuOps.php`

Contact
-------

Open an issue at: https://github.com/LudovicBoudi/Cyonima.ops.php.lib

---

End of detailed manual.

---
