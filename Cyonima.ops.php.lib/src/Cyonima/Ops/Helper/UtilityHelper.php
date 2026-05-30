<?php

declare(strict_types=1);

namespace Cyonima\Ops\Helper;

/**
 * Utility helper class for common operations
 */
class UtilityHelper
{
    /**
     * Check if password meets security requirements
     *
     * @param string $password Password to check
     * @return bool True if password is strong, false otherwise
     */
    public static function checkPasswordStrength(string $password): bool
    {
        // Check if the password is at least 12 characters long and contains at least:
        // - 2 uppercase letters
        // - 2 lowercase letters
        // - 2 digits
        // - 2 special characters (non-alphanumeric)

        if (strlen($password) < 12) {
            return false;
        }

        $uppercase = preg_match_all('/[A-Z]/', $password);
        $lowercase = preg_match_all('/[a-z]/', $password);
        $digits = preg_match_all('/[0-9]/', $password);
        $special = preg_match_all('/[^A-Za-z0-9]/', $password);

        return ($uppercase >= 2 && $lowercase >= 2 && $digits >= 2 && $special >= 2);
    }

    /**
     * Search and replace text in a file
     *
     * @param string $search Text to search for
     * @param string $replace Text to replace with
     * @param string $filepath Path to the file
     * @return void
     * @throws \RuntimeException if file cannot be read or written
     */
    public static function seekAndReplace(string $search, string $replace, string $filepath): void
    {
        if (!file_exists($filepath)) {
            throw new \RuntimeException("File not found: $filepath");
        }

        $content = file_get_contents($filepath);
        if ($content === false) {
            throw new \RuntimeException("Cannot read file: $filepath");
        }

        $newContent = str_replace($search, $replace, $content);
        if (file_put_contents($filepath, $newContent) === false) {
            throw new \RuntimeException("Cannot write to file: $filepath");
        }
    }

    /**
     * Read a line from standard input
     *
     * @param int $maxLength Maximum length of input to read
     * @return string The input line
     * @throws \RuntimeException if stdin cannot be read
     */
    public static function readStdIn(int $maxLength = 128): string
    {
        $stream = fopen("php://stdin", "r");
        if ($stream === false) {
            throw new \RuntimeException("Cannot open stdin");
        }

        $input = fgets($stream, $maxLength);
        fclose($stream);

        if ($input === false) {
            throw new \RuntimeException("Cannot read from stdin");
        }

        return rtrim($input);
    }
}
