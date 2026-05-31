<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Hyper-V helper module
 */
class HyperVOps extends AbstractWindowsOps
{
    public function createVM(string $name, int $memoryMB, string $vhdPath): RemoteCommandOutput
    {
        $script = 'New-VM -Name ' . self::escapePowerShellArgument($name) .
            ' -MemoryStartupBytes ' . self::escapePowerShellArgument((string) ($memoryMB * 1024 * 1024)) .
            ' -VHDPath ' . self::escapePowerShellArgument($vhdPath);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    public function startVM(string $name): RemoteCommandOutput
    {
        $script = 'Start-VM -Name ' . self::escapePowerShellArgument($name);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    public function stopVM(string $name): RemoteCommandOutput
    {
        $script = 'Stop-VM -Name ' . self::escapePowerShellArgument($name) . ' -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    public function removeVM(string $name): RemoteCommandOutput
    {
        $script = 'Remove-VM -Name ' . self::escapePowerShellArgument($name) . ' -Force';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }
}
