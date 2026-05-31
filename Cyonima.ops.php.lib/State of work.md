## Current status

### Done
- [x] Project skeleton and PSR-4 autoloading
- [x] Basic SSH/SCP core in `AbstractOps`
- [x] `RemoteCommandOutput` object with stdout/stderr/exit code handling
- [x] PSR-3 compatible `SimpleLogger`
- [x] `InputValidator` for hosts, credentials, files, ports and commands
- [x] Cisco network equipment class `Cyonima\Ops\Network\CiscoOps`
	- [x] Basic Cisco connection and command execution
	- [x] VLAN configuration and interface assignment
	- [x] Route and default gateway configuration
	- [x] ACL and NetFlow helper methods
- [x] Linux operations base class `AbstractLinuxOps`
	- [x] `systemctl` service control
	- [x] user creation / deletion / password change
	- [x] abstract package management interface
	- [x] Ubuntu/Debian `apt` implementation
	- [x] Red Hat `yum` implementation
- [x] Windows operations base class `AbstractWindowsOps`
	- [x] PowerShell over SSH command execution
	- [x] service management helpers
	- [x] local user management helpers
	- [x] package install/uninstall/upgrade via `winget` or `choco`
- [x] Native WinRM support with `WinRmClient` and `WindowsWinRmOps`
- [x] macOS operations base class `AbstractMacOsOps`
	- [x] Homebrew package management
	- [x] `brew services` control
	- [x] local account management via `sysadminctl`
	- [x] macOS version reporting
- [x] BSD operations base class `AbstractBsdOps`
	- [x] FreeBSD `pkg` implementation via `FreeBsdOps`
	- [x] OpenBSD `pkg_add` / `pkg_delete` implementation via `OpenBsdOps`
- [x] OpenStack cloud management
- [x] Azure operations base class `AbstractAzureOps`
	- [x] Azure CLI management helpers via `AzureOps`
- [x] GCP operations base class `AbstractGcpOps`
	- [x] gcloud CLI management helpers via `GcpOps`
- [x] AWS operations base class `AbstractAwsOps`
	- [x] AWS CLI management helpers via `AwsOps`
- [x] `README.md` updated with multi-platform usage examples

### In progress / short-term improvements
- [x] Linux: add file management helpers (`cp`, `mv`, `rm`, `chmod`, `chown`)
- [x] Linux: add process management helpers (`ps`, `kill`, `top`, `pgrep`)
- [x] Linux: add log management helpers (journalctl, log rotate support)
- [x] Linux: add network management helpers (`ip`, `nmcli`, `ifconfig`, route)
- [x] Linux: add firewall helpers (firewalld, ufw, nftables)
- [x] Linux: add SELinux/AppArmor management helpers
- [x] Linux: complete `SuseOps`, `FedoraOps`, `CentosOps`, `RockyOps` package manager implementations
 - [x] Linux: complete `SuseOps`, `FedoraOps`, `CentosOps`, `RockyOps` package manager implementations
 - [x] Linux: add support for Ubuntu derivatives `LinuxMintOps` and `ZorinOps`
 - [x] BSD: add support for `FreeBsdOps` and `OpenBsdOps`
 - [x] Cloud: add support for Azure CLI management via `AzureOps`
- [x] Windows: add file management helpers (`Copy-Item`, `Remove-Item`, `Get-ChildItem`)
- [x] Windows: add process management helpers (`Get-Process`, `Stop-Process`)
- [x] Windows: add log management helpers (`Get-EventLog`, `Get-WinEvent`)
- [x] Windows: add network helpers (`Get-NetIPAddress`, `Set-NetIPAddress`, `New-NetRoute`)
- [x] Windows: add firewall helpers (`New-NetFirewallRule`, `Set-NetFirewallProfile`)
 - [x] Windows: add file management helpers (`Copy-Item`, `Remove-Item`, `Get-ChildItem`)
 - [x] Windows: add process management helpers (`Get-Process`, `Stop-Process`)
 - [x] Windows: add log management helpers (`Get-EventLog`, `Get-WinEvent`)
 - [x] Windows: add network helpers (`Get-NetIPAddress`, `Set-NetIPAddress`, `New-NetRoute`)
 - [x] Windows: add firewall helpers (`New-NetFirewallRule`, `Set-NetFirewallProfile`)
 - [x] Windows: add Active Directory helpers for domain/OU/user management
 - [x] Windows: add IIS, SQL Server and Hyper-V helpers as separate modules
- [x] Add unit/integration tests for `WindowsOps`, `WindowsWinRmOps`, `MacOsOps`, `AbstractLinuxOps` (partial coverage added)

### Future modules / roadmap
- [ ] VMware module (`ESXi`, `vCenter`, VMs, storage, networking, snapshots)
- [ ] Juniper switch support
- [ ] StormShield, Fortinet, Palo Alto firewall support
- [ ] KVM/QEMU VM management
- [ ] Docker container management
- [ ] Kubernetes cluster management
- [ ] VirtualBox, Proxmox, Hyper-V, XenServer, Citrix platform modules

### Notes
- The codebase is currently focused on SSH-based remote execution and generic OS helpers.