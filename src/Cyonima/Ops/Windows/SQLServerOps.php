<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * SQL Server helper module
 */
class SQLServerOps extends AbstractWindowsOps
{
    /**
     * Run a single T-SQL query using Invoke-Sqlcmd (requires SqlServer module or SQLPS)
     */
    public function runQuery(string $serverInstance, string $database, string $query): RemoteCommandOutput
    {
        $encoded = base64_encode($query);
        $script = '$q = [System.Text.Encoding]::UTF8.GetString([System.Convert]::FromBase64String(' . self::escapePowerShellArgument($encoded) . ')); ' .
            'Invoke-Sqlcmd -Query $q -ServerInstance ' . self::escapePowerShellArgument($serverInstance) . ' -Database ' . self::escapePowerShellArgument($database);
        return $this->remoteExec($this->toPowerShellCommand($script));
    }
}
