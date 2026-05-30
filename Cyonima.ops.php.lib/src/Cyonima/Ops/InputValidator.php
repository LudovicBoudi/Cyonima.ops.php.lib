<?php

declare(strict_types=1);

namespace Cyonima\Ops;

/**
 * Validates input parameters to prevent security issues
 *
 * Note: While this class provides basic validation, it is still the developer's
 * responsibility to properly sanitize and validate all user inputs.
 */
class InputValidator
{
    /**
     * Validate a hostname or IP address
     *
     * @param string $host Hostname or IP address
     * @return void
     * @throws \InvalidArgumentException if host is invalid
     */
    public static function validateHost(string $host): void
    {
        if (empty($host)) {
            throw new \InvalidArgumentException("Host cannot be empty");
        }

        // Check for valid IP address
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return;
        }

        // Check for valid hostname/FQDN
        if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return;
        }

        // Allow localhost variants
        if (in_array($host, ['localhost', 'localhost.localdomain'], true)) {
            return;
        }

        throw new \InvalidArgumentException("Invalid hostname or IP address: $host");
    }

    /**
     * Validate a port number
     *
     * @param int $port Port number
     * @return void
     * @throws \InvalidArgumentException if port is invalid
     */
    public static function validatePort(int $port): void
    {
        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException("Port must be between 1 and 65535, got: $port");
        }
    }

    /**
     * Validate a username
     *
     * @param string $username Username
     * @return void
     * @throws \InvalidArgumentException if username is invalid
     */
    public static function validateUsername(string $username): void
    {
        if (empty($username)) {
            throw new \InvalidArgumentException("Username cannot be empty");
        }

        if (strlen($username) > 32) {
            throw new \InvalidArgumentException("Username too long (max 32 characters)");
        }

        // Allow alphanumeric, dots, hyphens, underscores
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
            throw new \InvalidArgumentException("Username contains invalid characters: $username");
        }
    }

    /**
     * Validate a file path exists
     *
     * @param string $path File path
     * @return void
     * @throws \InvalidArgumentException if file doesn't exist
     */
    public static function validateFileExists(string $path): void
    {
        if (empty($path)) {
            throw new \InvalidArgumentException("File path cannot be empty");
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found: $path");
        }
    }

    /**
     * Validate a file path is readable
     *
     * @param string $path File path
     * @return void
     * @throws \InvalidArgumentException if file is not readable
     */
    public static function validateFileReadable(string $path): void
    {
        self::validateFileExists($path);

        if (!is_readable($path)) {
            throw new \InvalidArgumentException("File is not readable: $path");
        }
    }

    /**
     * Validate a file path is writable
     *
     * @param string $path File path
     * @return void
     * @throws \InvalidArgumentException if file is not writable
     */
    public static function validateFileWritable(string $path): void
    {
        self::validateFileExists($path);

        if (!is_writable($path)) {
            throw new \InvalidArgumentException("File is not writable: $path");
        }
    }

    /**
     * Validate directory exists and is writable
     *
     * @param string $path Directory path
     * @return void
     * @throws \InvalidArgumentException if directory is invalid
     */
    public static function validateDirectory(string $path): void
    {
        if (empty($path)) {
            throw new \InvalidArgumentException("Directory path cannot be empty");
        }

        if (!is_dir($path)) {
            throw new \InvalidArgumentException("Directory not found: $path");
        }

        if (!is_writable($path)) {
            throw new \InvalidArgumentException("Directory is not writable: $path");
        }
    }

    /**
     * Validate a password (basic strength check)
     *
     * @param string $password Password to validate
     * @param int $minLength Minimum password length
     * @return void
     * @throws \InvalidArgumentException if password is too weak
     */
    public static function validatePassword(string $password, int $minLength = 8): void
    {
        if (empty($password)) {
            throw new \InvalidArgumentException("Password cannot be empty");
        }

        if (strlen($password) < $minLength) {
            throw new \InvalidArgumentException("Password too short (minimum $minLength characters)");
        }
    }

    /**
     * Validate SSH port number (usually 22 or high ports)
     *
     * @param int $port Port number
     * @return void
     * @throws \InvalidArgumentException if port is not suitable for SSH
     */
    public static function validateSshPort(int $port): void
    {
        self::validatePort($port);

        // Warn against well-known problematic ports
        $reserved = [20, 21, 23, 25, 53, 80, 110, 143, 443, 465, 587, 993, 995];
        if (in_array($port, $reserved, true) && $port !== 22) {
            throw new \InvalidArgumentException(
                "SSH port $port may conflict with other services. Use 22 or high ports (1024+)"
            );
        }
    }

    /**
     * Check if a string looks like it contains command injection attempts
     *
     * @param string $input Input to check
     * @return bool true if potentially dangerous patterns detected
     */
    public static function hasCommandInjectionPatterns(string $input): bool
    {
        // Look for common shell injection patterns
        $dangerous = [
            '/[;&|`$<>()]/',      // Shell operators
            '/\$\{/',             // Variable substitution
            '/`.*`/',             // Command substitution
            '/%0[0-9a-f]/i',      // URL encoding for newlines/semicolons
        ];

        foreach ($dangerous as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate a command string (basic injection detection)
     *
     * Note: This is a basic check. Always escape/quote command arguments properly.
     *
     * @param string $command Command to validate
     * @param bool $strict If true, throw exception; if false, return boolean
     * @return bool true if command looks safe
     * @throws \InvalidArgumentException if strict mode and dangerous patterns detected
     */
    public static function validateCommand(string $command, bool $strict = false): bool
    {
        if (empty($command)) {
            if ($strict) {
                throw new \InvalidArgumentException("Command cannot be empty");
            }
            return false;
        }

        $hasDangerous = self::hasCommandInjectionPatterns($command);

        if ($hasDangerous && $strict) {
            throw new \InvalidArgumentException(
                "Command contains potentially dangerous patterns. " .
                "Ensure all variables are properly escaped/quoted."
            );
        }

        return !$hasDangerous;
    }

    /**
     * Sanitize a filename to prevent path traversal
     *
     * @param string $filename Filename to sanitize
     * @return string Sanitized filename
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove path components
        $filename = basename($filename);

        // Remove potentially dangerous characters
        return preg_replace('/[^\w._-]/u', '', $filename);
    }

    /**
     * Validate VLAN number
     *
     * @param string $vlanNumber VLAN ID
     * @return void
     * @throws \InvalidArgumentException if VLAN is invalid
     */
    public static function validateVlanNumber(string $vlanNumber): void
    {
        if (!preg_match('/^\d+$/', $vlanNumber)) {
            throw new \InvalidArgumentException("VLAN number must be numeric: $vlanNumber");
        }

        $num = (int)$vlanNumber;
        if ($num < 1 || $num > 4094) {
            throw new \InvalidArgumentException("VLAN number must be between 1 and 4094, got: $num");
        }
    }

    /**
     * Validate IP address format
     *
     * @param string $ip IP address
     * @param bool $allowCidr Allow CIDR notation (x.x.x.x/xx)
     * @return void
     * @throws \InvalidArgumentException if IP is invalid
     */
    public static function validateIpAddress(string $ip, bool $allowCidr = false): void
    {
        if (empty($ip)) {
            throw new \InvalidArgumentException("IP address cannot be empty");
        }

        if ($allowCidr && str_contains($ip, '/')) {
            [$ipPart] = explode('/', $ip, 2);
            if (!filter_var($ipPart, FILTER_VALIDATE_IP)) {
                throw new \InvalidArgumentException("Invalid IP address: $ip");
            }
            return;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \InvalidArgumentException("Invalid IP address: $ip");
        }
    }
}
