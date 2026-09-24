<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * IIS helper module for Windows hosts
 */
class IISOps extends AbstractWindowsOps
{
    public function installIIS(): RemoteCommandOutput
    {
        $script = 'Install-WindowsFeature -Name Web-Server -IncludeManagementTools';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    public function createWebsite(string $name, string $physicalPath, int $port = 80): RemoteCommandOutput
    {
        $script = 'New-Website -Name ' . self::escapePowerShellArgument($name) .
            ' -PhysicalPath ' . self::escapePowerShellArgument($physicalPath) .
            ' -Port ' . self::escapePowerShellArgument((string) $port);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    public function startWebsite(string $name): RemoteCommandOutput
    {
        $script = 'Start-Website -Name ' . self::escapePowerShellArgument($name);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    public function stopWebsite(string $name): RemoteCommandOutput
    {
        $script = 'Stop-Website -Name ' . self::escapePowerShellArgument($name);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    public function recycleAppPool(string $appPool): RemoteCommandOutput
    {
        $script = 'Restart-WebAppPool -Name ' . self::escapePowerShellArgument($appPool);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }
}
