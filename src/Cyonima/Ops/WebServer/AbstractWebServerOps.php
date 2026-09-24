<?php

declare(strict_types=1);

namespace Cyonima\Ops\WebServer;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for WebServer operations (Nginx, Apache, etc.) over SSH
 */
abstract class AbstractWebServerOps extends AbstractOps
{
    /**
     * Get the web server version.
     *
     * @return RemoteCommandOutput
     */
    abstract public function getVersion(): RemoteCommandOutput;

    /**
     * Get the current status of the web server service.
     *
     * @return RemoteCommandOutput
     */
    abstract public function getStatus(): RemoteCommandOutput;

    /**
     * Reload the web server configuration gracefully.
     *
     * @return RemoteCommandOutput
     */
    abstract public function reload(): RemoteCommandOutput;

    /**
     * Restart the web server service.
     *
     * @return RemoteCommandOutput
     */
    abstract public function restart(): RemoteCommandOutput;

    /**
     * Test the web server configuration for syntax errors.
     *
     * @return RemoteCommandOutput
     */
    abstract public function testConfig(): RemoteCommandOutput;

    /**
     * Enable a site (virtual host) by name.
     *
     * @param string $name Site name
     * @return RemoteCommandOutput
     */
    abstract public function enableSite(string $name): RemoteCommandOutput;

    /**
     * Disable a site (virtual host) by name.
     *
     * @param string $name Site name
     * @return RemoteCommandOutput
     */
    abstract public function disableSite(string $name): RemoteCommandOutput;

    /**
     * Create a virtual host configuration file.
     *
     * @param string $name Configuration name
     * @param string $domain Server domain name
     * @param int $port Listening port (default: 80)
     * @param string $root Document root path (default: /var/www/html)
     * @param string|null $sslCert Path to SSL certificate (optional)
     * @param string|null $sslKey Path to SSL private key (optional)
     * @return RemoteCommandOutput
     */
    abstract public function createVhost(
        string $name,
        string $domain,
        int $port = 80,
        string $root = '/var/www/html',
        ?string $sslCert = null,
        ?string $sslKey = null
    ): RemoteCommandOutput;

    /**
     * Delete a virtual host configuration file.
     *
     * @param string $name Configuration name
     * @return RemoteCommandOutput
     */
    abstract public function deleteVhost(string $name): RemoteCommandOutput;

    /**
     * Get the last N lines of the access log.
     *
     * @param int $lines Number of lines to retrieve (default: 50)
     * @return RemoteCommandOutput
     */
    abstract public function getAccessLog(int $lines = 50): RemoteCommandOutput;

    /**
     * Get the last N lines of the error log.
     *
     * @param int $lines Number of lines to retrieve (default: 50)
     * @return RemoteCommandOutput
     */
    abstract public function getErrorLog(int $lines = 50): RemoteCommandOutput;

    /**
     * Start the web server service.
     *
     * @return RemoteCommandOutput
     */
    abstract public function start(): RemoteCommandOutput;

    /**
     * Stop the web server service.
     *
     * @return RemoteCommandOutput
     */
    abstract public function stop(): RemoteCommandOutput;
}
