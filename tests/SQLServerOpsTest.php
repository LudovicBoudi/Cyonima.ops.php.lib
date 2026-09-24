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
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->runQuery('localhost\\SQLEXPRESS', 'master', $query);

        $this->assertStringContainsString('Invoke-Sqlcmd', $ops->capturedCommand);
        $this->assertStringContainsString('-ServerInstance', $ops->capturedCommand);
        $this->assertStringContainsString('localhost\SQLEXPRESS', $ops->capturedCommand);
        $this->assertStringContainsString('-Database', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
