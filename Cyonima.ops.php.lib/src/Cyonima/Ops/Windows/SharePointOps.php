<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * SharePoint helper skeleton
 */
class SharePointOps extends AbstractWindowsOps
{
    public function getFarmStatus(): RemoteCommandOutput
    {
        $script = 'Get-SPFarm | Select-Object -Property BuildVersion,Products | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    // Additional SharePoint helpers can be added here.
}
