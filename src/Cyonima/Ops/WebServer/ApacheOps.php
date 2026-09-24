<?php

declare(strict_types=1);

namespace Cyonima\Ops\WebServer;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Apache web server operations over SSH
 *
 * Provides a direct interface to manage Apache sites, virtual hosts,
 * configuration, and service lifecycle.
 *
 * Example usage:
 * ```php
 * $apache = new ApacheOps();
 * $apache->setHost('web.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $apache->openConnection();
 * $apache->createVhost('example', 'example.com', 80, '/var/www/example');
 * $apache->enableSite('example');
 * $apache->reload();
 * $apache->closeConnection();
 * ```
 */
class ApacheOps extends AbstractWebServerOps
{
    public const string DEFAULT_AVAILABLE_DIR = '/etc/apache2/sites-available';
    public const string DEFAULT_ENABLED_DIR = '/etc/apache2/sites-enabled';
    public const string DEFAULT_LOG_DIR = '/var/log/apache2';

    /**
     * Get the installed Apache version.
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('apache2ctl -v');
    }

    /**
     * Get the current status of the Apache service.
     *
     * @return RemoteCommandOutput
     */
    public function getStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl is-active apache2');
    }

    /**
     * Reload Apache configuration gracefully.
     *
     * @return RemoteCommandOutput
     */
    public function reload(): RemoteCommandOutput
    {
        return $this->remoteExec('apache2ctl graceful');
    }

    /**
     * Restart the Apache service.
     *
     * @return RemoteCommandOutput
     */
    public function restart(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl restart apache2');
    }

    /**
     * Test Apache configuration for syntax errors.
     *
     * @return RemoteCommandOutput
     */
    public function testConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('apache2ctl configtest');
    }

    /**
     * Enable a site using a2ensite.
     *
     * @param string $name Site name
     * @return RemoteCommandOutput
     */
    public function enableSite(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);

        return $this->remoteExec('a2ensite ' . $name);
    }

    /**
     * Disable a site using a2dissite.
     *
     * @param string $name Site name
     * @return RemoteCommandOutput
     */
    public function disableSite(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);

        return $this->remoteExec('a2dissite ' . $name);
    }

    /**
     * Create an Apache virtual host configuration file and enable it.
     *
     * @param string $name Configuration name
     * @param string $domain Server domain name
     * @param int $port Listening port (default: 80)
     * @param string $root Document root path (default: /var/www/html)
     * @param string|null $sslCert Path to SSL certificate (optional)
     * @param string|null $sslKey Path to SSL private key (optional)
     * @return RemoteCommandOutput
     */
    public function createVhost(
        string $name,
        string $domain,
        int $port = 80,
        string $root = '/var/www/html',
        ?string $sslCert = null,
        ?string $sslKey = null
    ): RemoteCommandOutput {
        $name = self::escapeShellArgument($name);
        $domain = self::escapeShellArgument($domain);
        $port = self::escapeShellArgument((string)$port);
        $root = self::escapeShellArgument($root);

        $config = "<VirtualHost *:" . $port . ">\n";
        $config .= "    ServerName " . $domain . "\n";
        $config .= "    DocumentRoot " . $root . "\n";
        $config .= "\n";
        $config .= "    <Directory " . $root . ">\n";
        $config .= "        Options Indexes FollowSymLinks\n";
        $config .= "        AllowOverride All\n";
        $config .= "        Require all granted\n";
        $config .= "    </Directory>\n";
        $config .= "\n";
        $config .= "    ErrorLog \${APACHE_LOG_DIR}/error.log\n";
        $config .= "    CustomLog \${APACHE_LOG_DIR}/access.log combined\n";
        $config .= "</VirtualHost>\n";

        $sslConfig = '';

        if ($sslCert !== null && $sslKey !== null) {
            $sslCert = self::escapeShellArgument($sslCert);
            $sslKey = self::escapeShellArgument($sslKey);

            $sslConfig = "\n<VirtualHost *:443>\n";
            $sslConfig .= "    ServerName " . $domain . "\n";
            $sslConfig .= "    DocumentRoot " . $root . "\n";
            $sslConfig .= "\n";
            $sslConfig .= "    <Directory " . $root . ">\n";
            $sslConfig .= "        Options Indexes FollowSymLinks\n";
            $sslConfig .= "        AllowOverride All\n";
            $sslConfig .= "        Require all granted\n";
            $sslConfig .= "    </Directory>\n";
            $sslConfig .= "\n";
            $sslConfig .= "    SSLEngine on\n";
            $sslConfig .= "    SSLCertificateFile " . $sslCert . "\n";
            $sslConfig .= "    SSLCertificateKeyFile " . $sslKey . "\n";
            $sslConfig .= "\n";
            $sslConfig .= "    ErrorLog \${APACHE_LOG_DIR}/error.log\n";
            $sslConfig .= "    CustomLog \${APACHE_LOG_DIR}/access.log combined\n";
            $sslConfig .= "</VirtualHost>\n";
        }

        $config .= $sslConfig;

        $encoded = base64_encode($config);
        $path = self::escapeShellArgument(self::DEFAULT_AVAILABLE_DIR . '/' . $name . '.conf');
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . $path;

        $result = $this->remoteExec($cmd);

        if ($result->isSuccessful()) {
            return $this->enableSite($name);
        }

        return $result;
    }

    /**
     * Delete an Apache virtual host configuration.
     *
     * @param string $name Configuration name
     * @return RemoteCommandOutput
     */
    public function deleteVhost(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);
        $available = self::escapeShellArgument(self::DEFAULT_AVAILABLE_DIR);

        $result = $this->disableSite($name);

        if ($result->isSuccessful() || str_contains($result->getStdout(), 'already') || str_contains($result->getStderr(), 'already')) {
            return $this->remoteExec('rm -f ' . $available . '/' . $name . '.conf');
        }

        return $result;
    }

    /**
     * Get the last N lines of the Apache access log.
     *
     * @param int $lines Number of lines to retrieve (default: 50)
     * @return RemoteCommandOutput
     */
    public function getAccessLog(int $lines = 50): RemoteCommandOutput
    {
        $lines = self::escapeShellArgument((string)$lines);
        $logDir = self::escapeShellArgument(self::DEFAULT_LOG_DIR);

        return $this->remoteExec('tail -n ' . $lines . ' ' . $logDir . '/access.log');
    }

    /**
     * Get the last N lines of the Apache error log.
     *
     * @param int $lines Number of lines to retrieve (default: 50)
     * @return RemoteCommandOutput
     */
    public function getErrorLog(int $lines = 50): RemoteCommandOutput
    {
        $lines = self::escapeShellArgument((string)$lines);
        $logDir = self::escapeShellArgument(self::DEFAULT_LOG_DIR);

        return $this->remoteExec('tail -n ' . $lines . ' ' . $logDir . '/error.log');
    }

    /**
     * Start the Apache service.
     *
     * @return RemoteCommandOutput
     */
    public function start(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl start apache2');
    }

    /**
     * Stop the Apache service.
     *
     * @return RemoteCommandOutput
     */
    public function stop(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl stop apache2');
    }
}
