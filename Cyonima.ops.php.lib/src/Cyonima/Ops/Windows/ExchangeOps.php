<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Exchange helper skeleton
 *
 * Note: Exchange management requires Exchange Management Shell and elevated privileges.
 */
class ExchangeOps extends AbstractWindowsOps
{
    public function getExchangeVersion(): RemoteCommandOutput
    {
        $script = 'Get-ExchangeServer | Select-Object Name,AdminDisplayVersion | ConvertTo-Json -Compress';
        return $this->remoteExec($this->toPowerShellCommand($script));
    }

    // Additional Exchange helpers can be added here.
}
