<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Windows\SQLServerOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class SQLServerOpsTest extends TestCase
{
    public function testRunQueryInvokesInvokeSqlcmd(): void
    {
        $query = "SELECT 1;";
        $ops = new class() extends SQLServerOps {
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->assertStringContainsString('Invoke-Sqlcmd', $command);
                $this->assertStringContainsString('-ServerInstance ' . self::escapePowerShellArgument('localhost\\SQLEXPRESS'), $command);
                $this->assertStringContainsString('-Database ' . self::escapePowerShellArgument('master'), $command);
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->runQuery('localhost\\SQLEXPRESS', 'master', $query);
        $this->assertSame(0, $result->getExitCode());
    }
}
