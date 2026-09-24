<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class RemoteCommandOutputTest extends TestCase
{
    public function testAccessorsReturnExpectedValues(): void
    {
        $output = new RemoteCommandOutput("hello\nworld\n", "error details", 0);

        $this->assertSame("hello\nworld\n", $output->getStdout());
        $this->assertSame("error details", $output->getStderr());
        $this->assertSame(0, $output->getExitCode());
        $this->assertTrue($output->isSuccessful());
        $this->assertFalse($output->failed());
        $this->assertSame("hello\nworld", $output->getTrimmedOutput());
        $this->assertSame(["hello", "world"], $output->getLines());
        $this->assertSame("hello", $output->getFirstLine());
        $this->assertSame("world", $output->getLastLine());
        $this->assertTrue($output->contains("world"));
        $this->assertSame("hello\nworld\n", (string) $output);
    }

    public function testThrowIfFailedThrowsOnNonZeroExitCode(): void
    {
        $this->expectException(\Cyonima\Ops\Exception\ExecutionException::class);

        $output = new RemoteCommandOutput("", "fatal error", 1);
        $output->throwIfFailed();
    }
}
