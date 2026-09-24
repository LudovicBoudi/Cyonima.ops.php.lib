<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for Windows operations
 *
 * Provides common PowerShell-based management helpers for Windows hosts
 */
abstract class AbstractWindowsOps extends AbstractOps
{
    /**
     * Retrieve Windows version information
     *
     * @return RemoteCommandOutput
     */
    public function getWindowsVersion(): RemoteCommandOutput
    {
        $script = 'Get-CimInstance Win32_OperatingSystem | Select-Object Caption, Version, BuildNumber | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve a Windows service status
     *
     * @param string $serviceName
     * @return RemoteCommandOutput
     */
    public function getService(string $serviceName): RemoteCommandOutput
    {
        $script = 'Get-Service -Name ' . self::escapePowerShellArgument($serviceName) . ' | Select-Object Name, DisplayName, Status | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve a Windows service status with privilege elevation
     *
     * @param string $serviceName
     * @return RemoteCommandOutput
     */
    public function getServiceWithPrivilege(string $serviceName): RemoteCommandOutput
    {
        return $this->getService($serviceName);
    }

    /**
     * Manage a Windows service
     *
     * @param string $serviceName
     * @param string $action (start|stop|restart|status)
     * @return RemoteCommandOutput
     */
    public function manageService(string $serviceName, string $action): RemoteCommandOutput
    {
        $action = strtolower($action);
        $safeService = self::escapePowerShellArgument($serviceName);
        $safeAction = self::escapePowerShellArgument($action);
        $script = 'switch (' . $safeAction . ') {' .
            "'start' { Start-Service -Name " . $safeService . "; Get-Service -Name " . $safeService . " | Select-Object Name, Status }" .
            "'stop' { Stop-Service -Name " . $safeService . "; Get-Service -Name " . $safeService . " | Select-Object Name, Status }" .
            "'restart' { Restart-Service -Name " . $safeService . "; Get-Service -Name " . $safeService . " | Select-Object Name, Status }" .
            "'status' { Get-Service -Name " . $safeService . " | Select-Object Name, Status }" .
            "default { Write-Error 'Unsupported action: ' + " . $safeAction . "; exit 1 }" .
            '}';

        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Manage a Windows service with privilege elevation
     *
     * @param string $serviceName
     * @param string $action (start|stop|restart|status)
     * @return RemoteCommandOutput
     */
    public function manageServiceWithPrivilege(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    /**
     * Manage a Windows service with privilege elevation (no password required)
     *
     * @param string $serviceName
     * @param string $action (start|stop|restart|status)
     * @return RemoteCommandOutput
     */
    public function manageServiceWithPrivilegeNoPwd(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    /**
     * Create a local Windows user account
     *
     * @param string $username
     * @param string $password
     * @param string|null $group Optional local group to add the user to
     * @return RemoteCommandOutput
     */
    public function addUser(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        $safeUsername = self::escapePowerShellArgument($username);
        $safePassword = self::escapePowerShellArgument($password);
        $script = 'New-LocalUser -Name ' . $safeUsername .
            ' -Password (ConvertTo-SecureString ' . $safePassword . ' -AsPlainText -Force)' .
            ' -PasswordNeverExpires:$true -UserMayNotChangePassword:$false';

        if ($group !== null && trim($group) !== '') {
            $script .= ' ; Add-LocalGroupMember -Group ' . self::escapePowerShellArgument($group) . ' -Member ' . $safeUsername;
        }

        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Create a local Windows user account with privilege elevation
     *
     * @param string $username
     * @param string $password
     * @param string|null $group Optional local group to add the user to
     * @return RemoteCommandOutput
     */
    public function addUserWithPrivilege(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        return $this->addUser($username, $password, $group);
    }

    /**
     * Delete a local Windows user account
     *
     * @param string $username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        $script = 'Remove-LocalUser -Name ' . self::escapePowerShellArgument($username) . ' -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Delete a local Windows user account with privilege elevation
     *
     * @param string $username
     * @return RemoteCommandOutput
     */
    public function deleteUserWithPrivilege(string $username): RemoteCommandOutput
    {
        return $this->deleteUser($username);
    }

    /**
     * Change a local Windows user password
     *
     * @param string $username
     * @param string $newPassword
     * @return RemoteCommandOutput
     */
    public function changePassword(string $username, string $newPassword): RemoteCommandOutput
    {
        $safeUsername = self::escapePowerShellArgument($username);
        $safePassword = self::escapePowerShellArgument($newPassword);
        $script = 'Set-LocalUser -Name ' . $safeUsername . ' -Password (ConvertTo-SecureString ' . $safePassword . ' -AsPlainText -Force)';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Change a local Windows user password with privilege elevation
     *
     * @param string $username
     * @param string $newPassword
     * @return RemoteCommandOutput
     */
    public function changePasswordWithPrivilege(string $username, string $newPassword): RemoteCommandOutput
    {
        return $this->changePassword($username, $newPassword);
    }

    /**
     * Join the machine to an Active Directory domain
     *
     * @param string $domain
     * @param string $credentialUser
     * @param string $credentialPassword
     * @param string|null $ou Optional OU distinguished name
     * @return RemoteCommandOutput
     */
    public function joinDomain(string $domain, string $credentialUser, string $credentialPassword, ?string $ou = null): RemoteCommandOutput
    {
        $script = '$pass = ConvertTo-SecureString ' . self::escapePowerShellArgument($credentialPassword) . ' -AsPlainText -Force; ' .
            '$cred = New-Object System.Management.Automation.PSCredential(' . self::escapePowerShellArgument($credentialUser) . ', $pass); ' .
            'Add-Computer -DomainName ' . self::escapePowerShellArgument($domain) . ' -Credential $cred -Restart:$false';
        if ($ou !== null && trim($ou) !== '') {
            $script .= ' -OUPath ' . self::escapePowerShellArgument($ou);
        }
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Create an Active Directory user (requires RSAT/AD module on host)
     *
     * @param string $samAccountName
     * @param string $displayName
     * @param string $password
     * @param string|null $ouPath
     * @return RemoteCommandOutput
     */
    public function createAdUser(string $samAccountName, string $displayName, string $password, ?string $ouPath = null): RemoteCommandOutput
    {
        $script = 'Import-Module ActiveDirectory; ' .
            'New-ADUser -Name ' . self::escapePowerShellArgument($displayName) .
            ' -SamAccountName ' . self::escapePowerShellArgument($samAccountName) .
            ' -AccountPassword (ConvertTo-SecureString ' . self::escapePowerShellArgument($password) . ' -AsPlainText -Force) -Enabled $true';
        if ($ouPath !== null && trim($ouPath) !== '') {
            $script .= ' -Path ' . self::escapePowerShellArgument($ouPath);
        }
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Add an existing AD user to a group
     *
     * @param string $samAccountName
     * @param string $groupName
     * @return RemoteCommandOutput
     */
    public function addAdUserToGroup(string $samAccountName, string $groupName): RemoteCommandOutput
    {
        $script = 'Import-Module ActiveDirectory; Add-ADGroupMember -Identity ' . self::escapePowerShellArgument($groupName) . ' -Members ' . self::escapePowerShellArgument($samAccountName);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Create an Organizational Unit in AD
     *
     * @param string $ouName
     * @param string|null $parentDn
     * @return RemoteCommandOutput
     */
    public function createOrganizationalUnit(string $ouName, ?string $parentDn = null): RemoteCommandOutput
    {
        $script = 'Import-Module ActiveDirectory; New-ADOrganizationalUnit -Name ' . self::escapePowerShellArgument($ouName);
        if ($parentDn !== null && trim($parentDn) !== '') {
            $script .= ' -Path ' . self::escapePowerShellArgument($parentDn);
        }
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Read a file from the remote Windows host
     *
     * @param string $path File path
     * @return RemoteCommandOutput
     */
    public function readFile(string $path): RemoteCommandOutput
    {
        $script = 'Get-Content -Path ' . self::escapePowerShellArgument($path) . ' -Raw | Out-String';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Write content to a remote Windows file
     *
     * @param string $path File path
     * @param string $content File content
     * @return RemoteCommandOutput
     */
    public function writeFile(string $path, string $content): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $script = '$decoded = [System.Text.Encoding]::UTF8.GetString([System.Convert]::FromBase64String(' . self::escapePowerShellArgument($encoded) . ')); ' .
            'Set-Content -Path ' . self::escapePowerShellArgument($path) . ' -Value $decoded -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Copy a file on the remote Windows host
     *
     * @param string $source Source file path
     * @param string $destination Destination file path
     * @return RemoteCommandOutput
     */
    public function copyFile(string $source, string $destination): RemoteCommandOutput
    {
        $script = 'Copy-Item -Path ' . self::escapePowerShellArgument($source) . ' -Destination ' . self::escapePowerShellArgument($destination) . ' -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Move or rename a file on the remote Windows host
     *
     * @param string $source Source file path
     * @param string $destination Destination file path
     * @return RemoteCommandOutput
     */
    public function moveFile(string $source, string $destination): RemoteCommandOutput
    {
        $script = 'Move-Item -Path ' . self::escapePowerShellArgument($source) . ' -Destination ' . self::escapePowerShellArgument($destination) . ' -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Remove a file or directory on the remote Windows host
     *
     * @param string $path File or directory path
     * @return RemoteCommandOutput
     */
    public function removeFile(string $path): RemoteCommandOutput
    {
        $script = 'Remove-Item -Path ' . self::escapePowerShellArgument($path) . ' -Recurse -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve running Windows processes
     *
     * @param string $name Optional process name filter
     * @return RemoteCommandOutput
     */
    public function getProcesses(string $name = ''): RemoteCommandOutput
    {
        $script = 'Get-Process';
        if ($name !== '') {
            $script .= ' -Name ' . self::escapePowerShellArgument($name);
        }
        $script .= ' | Select-Object Id, ProcessName, CPU, WorkingSet | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Stop a Windows process by PID
     *
     * @param int $pid Process ID
     * @return RemoteCommandOutput
     */
    public function killProcessById(int $pid): RemoteCommandOutput
    {
        $script = 'Stop-Process -Id ' . self::escapePowerShellArgument((string) $pid) . ' -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Stop a Windows process by name
     *
     * @param string $name Process name
     * @return RemoteCommandOutput
     */
    public function killProcessByName(string $name): RemoteCommandOutput
    {
        $script = 'Get-Process -Name ' . self::escapePowerShellArgument($name) . ' | Stop-Process -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve Windows Event Log entries
     *
     * @param string $logName Event log name
     * @param int $maxEvents Maximum number of events
     * @return RemoteCommandOutput
     */
    public function getEventLog(string $logName = 'Application', int $maxEvents = 100): RemoteCommandOutput
    {
        $script = 'Get-EventLog -LogName ' . self::escapePowerShellArgument($logName) . ' -Newest ' . self::escapePowerShellArgument((string) $maxEvents) . ' | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve Windows event logs using Get-WinEvent
     *
     * @param string $query Query string
     * @param int $maxEvents Maximum number of events
     * @return RemoteCommandOutput
     */
    public function getWinEvent(string $query = '*', int $maxEvents = 100): RemoteCommandOutput
    {
        $script = 'Get-WinEvent -FilterXPath ' . self::escapePowerShellArgument($query) . ' -MaxEvents ' . self::escapePowerShellArgument((string) $maxEvents) . ' | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve Windows network adapter addresses
     *
     * @return RemoteCommandOutput
     */
    public function getNetworkInterfaces(): RemoteCommandOutput
    {
        $script = 'Get-NetIPAddress | Select-Object InterfaceAlias,IPAddress,PrefixLength,AddressFamily | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve Windows network routes
     *
     * @return RemoteCommandOutput
     */
    public function getNetworkRoutes(): RemoteCommandOutput
    {
        $script = 'Get-NetRoute | Select-Object DestinationPrefix,NextHop,InterfaceAlias,RouteMetric | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Add a Windows network route
     *
     * @param string $destinationPrefix Address prefix
     * @param string $nextHop Next hop address
     * @param string|null $interfaceAlias Optional interface alias
     * @return RemoteCommandOutput
     */
    public function addNetworkRoute(string $destinationPrefix, string $nextHop, ?string $interfaceAlias = null): RemoteCommandOutput
    {
        $script = 'New-NetRoute -DestinationPrefix ' . self::escapePowerShellArgument($destinationPrefix) . ' -NextHop ' . self::escapePowerShellArgument($nextHop);
        if ($interfaceAlias !== null && trim($interfaceAlias) !== '') {
            $script .= ' -InterfaceAlias ' . self::escapePowerShellArgument($interfaceAlias);
        }
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Retrieve Windows firewall rules
     *
     * @return RemoteCommandOutput
     */
    public function getFirewallRules(): RemoteCommandOutput
    {
        $script = 'Get-NetFirewallRule | Select-Object Name,DisplayName,Enabled,Direction,Action | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Add a Windows firewall rule
     *
     * @param string $name Rule name
     * @param string $displayName Display name
     * @param string $direction Rule direction (Inbound/Outbound)
     * @param string $protocol Protocol (TCP/UDP)
     * @param string $localPort Local port or port range
     * @param string $action Rule action (Allow/Deny)
     * @return RemoteCommandOutput
     */
    public function addFirewallRule(string $name, string $displayName, string $direction, string $protocol, string $localPort, string $action = 'Allow'): RemoteCommandOutput
    {
        $script = 'New-NetFirewallRule -Name ' . self::escapePowerShellArgument($name) .
            ' -DisplayName ' . self::escapePowerShellArgument($displayName) .
            ' -Direction ' . self::escapePowerShellArgument($direction) .
            ' -Action ' . self::escapePowerShellArgument($action) .
            ' -Protocol ' . self::escapePowerShellArgument($protocol) .
            ' -LocalPort ' . self::escapePowerShellArgument($localPort) .
            ' -Enabled True';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Enable or disable the Windows firewall
     *
     * @param bool $enabled True to enable, false to disable
     * @return RemoteCommandOutput
     */
    public function setFirewallEnabled(bool $enabled): RemoteCommandOutput
    {
        $script = ($enabled ? 'Set-NetFirewallProfile -Profile Domain,Public,Private -Enabled True' : 'Set-NetFirewallProfile -Profile Domain,Public,Private -Enabled False');
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Install a Windows package using winget or Chocolatey if available
     *
     * @param string $packageId
     * @return RemoteCommandOutput
     */
    public function installPackage(string $packageId): RemoteCommandOutput
    {
        return $this->remoteExec($this->toPowerShellCommand($this->buildPackageManagerCommand($packageId, 'install')));
    }

    /**
     * Install a Windows package with privilege elevation
     *
     * @param string $packageId
     * @return RemoteCommandOutput
     */
    public function installPackageWithPrivilege(string $packageId): RemoteCommandOutput
    {
        return $this->installPackage($packageId);
    }

    /**
     * Uninstall a Windows package using winget or Chocolatey if available
     *
     * @param string $packageId
     * @return RemoteCommandOutput
     */
    public function uninstallPackage(string $packageId): RemoteCommandOutput
    {
        return $this->remoteExec($this->toPowerShellCommand($this->buildPackageManagerCommand($packageId, 'uninstall')));
    }

    /**
     * Uninstall a Windows package with privilege elevation
     *
     * @param string $packageId
     * @return RemoteCommandOutput
     */
    public function uninstallPackageWithPrivilege(string $packageId): RemoteCommandOutput
    {
        return $this->uninstallPackage($packageId);
    }

    /**
     * Upgrade all installed Windows packages using winget or Chocolatey
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackages(): RemoteCommandOutput
    {
        $script = <<<'POWERSHELL'
if (Get-Command winget -ErrorAction SilentlyContinue) {
    winget upgrade --all --accept-source-agreements --accept-package-agreements
} elseif (Get-Command choco -ErrorAction SilentlyContinue) {
    choco upgrade all -y
} else {
    Write-Error 'No supported Windows package manager found.'
    exit 1
}
POWERSHELL;
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    /**
     * Upgrade all installed Windows packages with privilege elevation
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackagesWithPrivilege(): RemoteCommandOutput
    {
        return $this->upgradePackages();
    }

    /**
     * Convert a PowerShell expression into a remote command string
     *
     * @param string $script
     * @return string
     */
    protected function toPowerShellCommand(string $script): string
    {
        return 'powershell -NoProfile -NonInteractive -Command ' . self::escapeShellArgument($script);
    }

    /**
     * Escape a PowerShell argument safely for use inside a PowerShell script
     *
     * @param string $argument
     * @return string
     */
    protected static function escapePowerShellArgument(string $argument): string
    {
        return "'" . str_replace("'", "''", $argument) . "'";
    }

    /**
     * Build a package manager command for winget or Chocolatey
     *
     * @param string $packageId
     * @param string $operation
     * @return string
     */
    protected function buildPackageManagerCommand(string $packageId, string $operation): string
    {
        $safePackageId = self::escapePowerShellArgument($packageId);
        $safeOperation = strtolower($operation);

        if ($safeOperation === 'install') {
            $script = <<<'POWERSHELL'
if (Get-Command winget -ErrorAction SilentlyContinue) {
    winget install --accept-source-agreements --accept-package-agreements --id %PACKAGE_ID% -e
} elseif (Get-Command choco -ErrorAction SilentlyContinue) {
    choco install %PACKAGE_ID% -y
} else {
    Write-Error 'No supported Windows package manager found.'
    exit 1
}
POWERSHELL;
            return str_replace('%PACKAGE_ID%', $safePackageId, $script);
        }

        if ($safeOperation === 'uninstall') {
            $script = <<<'POWERSHELL'
if (Get-Command winget -ErrorAction SilentlyContinue) {
    winget uninstall --id %PACKAGE_ID% -e
} elseif (Get-Command choco -ErrorAction SilentlyContinue) {
    choco uninstall %PACKAGE_ID% -y
} else {
    Write-Error 'No supported Windows package manager found.'
    exit 1
}
POWERSHELL;
            return str_replace('%PACKAGE_ID%', $safePackageId, $script);
        }

        throw new \InvalidArgumentException("Unsupported package operation: $operation");
    }
}
