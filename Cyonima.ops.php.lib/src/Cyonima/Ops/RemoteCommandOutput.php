<?php

declare(strict_types=1);

namespace Cyonima\Ops;

/**
 * Encapsulates the output of a remote command execution
 *
 * Instead of returning raw strings, remote commands now return structured objects
 * with stdout, stderr, and exit code available separately.
 */
class RemoteCommandOutput
{
    /**
     * Constructor
     *
     * @param string $stdout Standard output from the command
     * @param string $stderr Standard error from the command
     * @param int $exitCode Exit code from the command (0 = success)
     */
    public function __construct(
        private string $stdout = '',
        private string $stderr = '',
        private int $exitCode = 0
    ) {
    }

    /**
     * Get the standard output
     *
     * @return string
     */
    public function getStdout(): string
    {
        return $this->stdout;
    }

    /**
     * Get the standard error output
     *
     * @return string
     */
    public function getStderr(): string
    {
        return $this->stderr;
    }

    /**
     * Get the exit code
     *
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * Check if the command was successful (exit code 0)
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->exitCode === 0;
    }

    /**
     * Check if the command failed (exit code != 0)
     *
     * @return bool
     */
    public function failed(): bool
    {
        return !$this->isSuccessful();
    }

    /**
     * Get the output as a string (stdout, or stderr if stdout is empty)
     *
     * @return string
     */
    public function __toString(): string
    {
        return !empty($this->stdout) ? $this->stdout : $this->stderr;
    }

    /**
     * Throw an exception if the command failed
     *
     * @return void
     * @throws Exception\ExecutionException if command failed
     */
    public function throwIfFailed(): void
    {
        if ($this->failed()) {
            $message = $this->stderr ?: "Command failed with exit code {$this->exitCode}";
            throw new Exception\ExecutionException(trim($message));
        }
    }

    /**
     * Get trimmed stdout (removes leading/trailing whitespace)
     *
     * @return string
     */
    public function getTrimmedOutput(): string
    {
        return trim($this->stdout);
    }

    /**
     * Get stdout as lines (split by newline)
     *
     * @return array<string>
     */
    public function getLines(): array
    {
        $output = $this->getTrimmedOutput();
        return empty($output) ? [] : explode("\n", $output);
    }

    /**
     * Get first line of output
     *
     * @return string
     */
    public function getFirstLine(): string
    {
        $lines = $this->getLines();
        return $lines[0] ?? '';
    }

    /**
     * Get last line of output
     *
     * @return string
     */
    public function getLastLine(): string
    {
        $lines = $this->getLines();
        return end($lines) ?: '';
    }

    /**
     * Check if output contains a string
     *
     * @param string $needle
     * @return bool
     */
    public function contains(string $needle): bool
    {
        return str_contains($this->stdout, $needle) || str_contains($this->stderr, $needle);
    }

    /**
     * Create an instance from SSH2 stream result
     *
     * @param mixed $stream SSH2 stream resource
     * @return self
     */
    public static function fromStream($stream): self
    {
        if ($stream === false) {
            return new self('', 'SSH stream error', 1);
        }

        $stderrStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($stream, true);
        if ($stderrStream !== false) {
            stream_set_blocking($stderrStream, true);
        }

        $stdout = stream_get_contents($stream);
        $stderr = $stderrStream !== false ? stream_get_contents($stderrStream) : '';

        $exitCode = ssh2_get_exit_status($stream);
        if ($exitCode === false) {
            $exitCode = 0;
        }

        fclose($stream);
        if ($stderrStream !== false) {
            fclose($stderrStream);
        }

        return new self($stdout, $stderr, $exitCode);
    }
}
