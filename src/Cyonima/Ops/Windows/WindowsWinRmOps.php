<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Native Windows operations over WinRM
 *
 * This class provides methods equivalent to WindowsOps, but uses WinRM instead
 * of SSH to execute PowerShell commands on a Windows host.
 */
class WindowsWinRmOps
{
    private WinRmClient $client;

    public function __construct(WinRmClient $client)
    {
        $this->client = $client;
    }

    public function getWindowsVersion(): RemoteCommandOutput
    {
        $script = 'Get-CimInstance Win32_OperatingSystem | Select-Object Caption, Version, BuildNumber | ConvertTo-Json -Compress';
        return $this->client->executePowerShell($script);
    }

    public function getService(string $serviceName): RemoteCommandOutput
    {
        $script = 'Get-Service -Name ' . $this->escapePowerShellArgument($serviceName) . ' | Select-Object Name, DisplayName, Status | ConvertTo-Json -Compress';
        return $this->client->executePowerShell($script);
    }

    public function getServiceWithPrivilege(string $serviceName): RemoteCommandOutput
    {
        return $this->getService($serviceName);
    }

    public function manageService(string $serviceName, string $action): RemoteCommandOutput
    {
        $action = strtolower($action);
        $s = $this->escapePowerShellArgument($serviceName);
        $a = $this->escapePowerShellArgument($action);

        $script = sprintf(
            "switch (%s) {'start' { Start-Service -Name %s; Get-Service -Name %s | Select-Object Name, Status } 'stop' { Stop-Service -Name %s; Get-Service -Name %s | Select-Object Name, Status } 'restart' { Restart-Service -Name %s; Get-Service -Name %s | Select-Object Name, Status } 'status' { Get-Service -Name %s | Select-Object Name, Status } default { Write-Error 'Unsupported action: %s'; exit 1 } }",
            $a, $s, $s, $s, $s, $s, $s, $s, $a
        );

        return $this->client->executePowerShell($script);
    }

    public function manageServiceWithPrivilege(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    public function manageServiceWithPrivilegeNoPwd(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    public function addUser(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        $safeUsername = $this->escapePowerShellArgument($username);
        $safePassword = $this->escapePowerShellArgument($password);
        $script = 'New-LocalUser -Name ' . $safeUsername .
            ' -Password (ConvertTo-SecureString ' . $safePassword . ' -AsPlainText -Force)' .
            ' -PasswordNeverExpires:$true -UserMayNotChangePassword:$false';

        if ($group !== null && trim($group) !== '') {
            $script .= ' ; Add-LocalGroupMember -Group ' . $this->escapePowerShellArgument($group) . ' -Member ' . $safeUsername;
        }

        return $this->client->executePowerShell($script);
    }

    public function addUserWithPrivilege(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        return $this->addUser($username, $password, $group);
    }

    public function deleteUser(string $username): RemoteCommandOutput
    {
        $script = 'Remove-LocalUser -Name ' . $this->escapePowerShellArgument($username) . ' -Force';
        return $this->client->executePowerShell($script);
    }

    public function deleteUserWithPrivilege(string $username): RemoteCommandOutput
    {
        return $this->deleteUser($username);
    }

    public function changePassword(string $username, string $newPassword): RemoteCommandOutput
    {
        $script = 'Set-LocalUser -Name ' . $this->escapePowerShellArgument($username) .
            ' -Password (ConvertTo-SecureString ' . $this->escapePowerShellArgument($newPassword) . ' -AsPlainText -Force)';
        return $this->client->executePowerShell($script);
    }

    public function changePasswordWithPrivilege(string $username, string $newPassword): RemoteCommandOutput
    {
        return $this->changePassword($username, $newPassword);
    }

    public function installPackage(string $packageId): RemoteCommandOutput
    {
        return $this->client->executePowerShell($this->buildPackageManagerScript($packageId, 'install'));
    }

    public function installPackageWithPrivilege(string $packageId): RemoteCommandOutput
    {
        return $this->installPackage($packageId);
    }

    public function uninstallPackage(string $packageId): RemoteCommandOutput
    {
        return $this->client->executePowerShell($this->buildPackageManagerScript($packageId, 'uninstall'));
    }

    public function uninstallPackageWithPrivilege(string $packageId): RemoteCommandOutput
    {
        return $this->uninstallPackage($packageId);
    }

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
        return $this->client->executePowerShell($script);
    }

    public function upgradePackagesWithPrivilege(): RemoteCommandOutput
    {
        return $this->upgradePackages();
    }

    protected function buildPackageManagerScript(string $packageId, string $operation): string
    {
        $safePackageId = $this->escapePowerShellArgument($packageId);
        $operation = strtolower($operation);

        if ($operation === 'install') {
            return <<<POWERSHELL
if (Get-Command winget -ErrorAction SilentlyContinue) {
    winget install --accept-source-agreements --accept-package-agreements --id $safePackageId -e
} elseif (Get-Command choco -ErrorAction SilentlyContinue) {
    choco install $safePackageId -y
} else {
    Write-Error 'No supported Windows package manager found.'
    exit 1
}
POWERSHELL;
        }

        if ($operation === 'uninstall') {
            return <<<POWERSHELL
if (Get-Command winget -ErrorAction SilentlyContinue) {
    winget uninstall --id $safePackageId -e
} elseif (Get-Command choco -ErrorAction SilentlyContinue) {
    choco uninstall $safePackageId -y
} else {
    Write-Error 'No supported Windows package manager found.'
    exit 1
}
POWERSHELL;
        }

        throw new \InvalidArgumentException("Unsupported package operation: $operation");
    }

    protected function escapePowerShellArgument(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }
}
