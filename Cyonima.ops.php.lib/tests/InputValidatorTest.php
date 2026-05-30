<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\InputValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InputValidatorTest extends TestCase
{
    public function testValidateHostAcceptsValidHostnames(): void
    {
        InputValidator::validateHost('localhost');
        InputValidator::validateHost('127.0.0.1');
        InputValidator::validateHost('example.com');
        $this->assertTrue(true);
    }

    public function testValidateHostRejectsInvalidHost(): void
    {
        $this->expectException(InvalidArgumentException::class);
        InputValidator::validateHost('invalid host');
    }

    public function testValidateCommandRejectsDangerousInputInStrictMode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        InputValidator::validateCommand('rm -rf /', true);
    }

    public function testSanitizeFilenameRemovesDangerousCharacters(): void
    {
        $this->assertSame('passwd', InputValidator::sanitizeFilename('../etc/passwd'));
        $this->assertSame('safe_name.txt', InputValidator::sanitizeFilename('safe_name.txt'));
    }
}
