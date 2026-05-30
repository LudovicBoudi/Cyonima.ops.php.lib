<?php

declare(strict_types=1);

namespace Cyonima\Ops\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Simple PSR-3 compliant logger implementation
 *
 * This logger can write to files, stderr, or be replaced with Monolog/other PSR-3 loggers.
 */
class SimpleLogger implements LoggerInterface
{
    private string $logFile;
    private string $logLevel = LogLevel::INFO;
    private array $logLevels = [
        LogLevel::DEBUG => 0,
        LogLevel::INFO => 1,
        LogLevel::NOTICE => 2,
        LogLevel::WARNING => 3,
        LogLevel::ERROR => 4,
        LogLevel::CRITICAL => 5,
        LogLevel::ALERT => 6,
        LogLevel::EMERGENCY => 7,
    ];

    /**
     * Constructor
     *
     * @param string|null $logFile Path to log file (if null, logs to stderr)
     * @param string $minLogLevel Minimum log level to record (default: INFO)
     */
    public function __construct(?string $logFile = null, string $minLogLevel = LogLevel::INFO)
    {
        $this->logFile = $logFile ?? 'php://stderr';
        $this->logLevel = $minLogLevel;
    }

    /**
     * Log a message at the given level
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, string $message, array $context = []): void
    {
        // Check if we should log this level
        if (!isset($this->logLevels[$level])) {
            $level = LogLevel::INFO;
        }

        if ($this->logLevels[$level] < $this->logLevels[$this->logLevel]) {
            return; // Skip if below minimum level
        }

        // Interpolate context into message
        $message = $this->interpolate($message, $context);

        // Format the log entry
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [" . strtoupper($level) . "] $message\n";

        // Write to file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    /**
     * System is unusable
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Interpolate context values into message placeholders
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $value) {
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replace['{' . $key . '}'] = $value;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Set minimum log level
     *
     * @param string $level Log level
     * @return self
     */
    public function setMinLogLevel(string $level): self
    {
        if (!isset($this->logLevels[$level])) {
            throw new \InvalidArgumentException("Invalid log level: $level");
        }
        $this->logLevel = $level;
        return $this;
    }

    /**
     * Get current log file path
     *
     * @return string
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }
}
