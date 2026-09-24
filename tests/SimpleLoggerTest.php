<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Logger\SimpleLogger;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SimpleLoggerTest extends TestCase
{
    public function testLogCreatesFileAndAppendsEntries(): void
    {
        $tmpFile = sys_get_temp_dir() . '/cyonima_simple_logger_test.log';
        @unlink($tmpFile);

        $logger = new SimpleLogger($tmpFile, 'debug');
        $logger->info('Hello {name}', ['name' => 'World']);
        $logger->error('Failure occurred: {reason}', ['reason' => 'test']);

        $this->assertFileExists($tmpFile);
        $content = file_get_contents($tmpFile);

        $this->assertStringContainsString('[INFO] Hello World', $content);
        $this->assertStringContainsString('[ERROR] Failure occurred: test', $content);

        @unlink($tmpFile);
    }

    public function testSetMinLogLevelRejectsInvalidLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $logger = new SimpleLogger('php://memory');
        $logger->setMinLogLevel('invalid.level');
    }
}
