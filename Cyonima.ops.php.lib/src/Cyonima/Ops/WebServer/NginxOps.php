<?php

declare(strict_types=1);

namespace Cyonima\Ops\WebServer;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Nginx web server operations over SSH
 *
 * Provides a direct interface to manage Nginx sites, virtual hosts,
 * configuration, and service lifecycle.
 *
 * Example usage:
 * ```php
 * $nginx = new NginxOps();
 * $nginx->setHost('web.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $nginx->openConnection();
 * $nginx->createVhost('example', 'example.com', 80, '/var/www/example');
 * $nginx->enableSite('example');
 * $nginx->reload();
 * $nginx->closeConnection();
 * ```
 */
class NginxOps extends AbstractWebServerOps
{
    public const string DEFAULT_AVAILABLE_DIR = '/etc/nginx/sites-available';
    public const string DEFAULT_ENABLED_DIR = '/etc/nginx/sites-enabled';
    public const string DEFAULT_LOG_DIR = '/var/log/nginx';

    /**
     * Get the installed Nginx version.
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('nginx -v 2>&1');
    }

    /**
     * Get the current status of the Nginx service.
     *
     * @return RemoteCommandOutput
     */
    public function getStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl is-active nginx');
    }

    /**
     * Reload Nginx configuration gracefully.
     *
     * @return RemoteCommandOutput
     */
    public function reload(): RemoteCommandOutput
    {
        return $this->remoteExec('nginx -s reload');
    }

    /**
     * Restart the Nginx service.
     *
     * @return RemoteCommandOutput
     */
    public function restart(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl restart nginx');
    }

    /**
     * Test Nginx configuration for syntax errors.
     *
     * @return RemoteCommandOutput
     */
    public function testConfig(): RemoteCommandOutput
    {
        return $this->remoteExec('nginx -t');
    }

    /**
     * Enable a site by creating a symlink in sites-enabled.
     *
     * @param string $name Site name
     * @return RemoteCommandOutput
     */
    public function enableSite(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);
        $available = self::escapeShellArgument(self::DEFAULT_AVAILABLE_DIR);
        $enabled = self::escapeShellArgument(self::DEFAULT_ENABLED_DIR);

        return $this->remoteExec('ln -sf ' . $available . '/' . $name . ' ' . $enabled . '/' . $name);
    }

    /**
     * Disable a site by removing the symlink from sites-enabled.
     *
     * @param string $name Site name
     * @return RemoteCommandOutput
     */
    public function disableSite(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);
        $enabled = self::escapeShellArgument(self::DEFAULT_ENABLED_DIR);

        return $this->remoteExec('rm -f ' . $enabled . '/' . $name);
    }

    /**
     * Create an Nginx virtual host configuration file and enable it.
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

        $config = "server {\n";
        $config .= "    listen " . $port . ";\n";
        $config .= "    server_name " . $domain . ";\n";
        $config .= "    root " . $root . ";\n";
        $config .= "\n";
        $config .= "    index index.html index.htm index.php;\n";
        $config .= "\n";
        $config .= "    location / {\n";
        $config .= "        try_files \$uri \$uri/ =404;\n";
        $config .= "    }\n";
        $config .= "}\n";

        $sslConfig = '';

        if ($sslCert !== null && $sslKey !== null) {
            $sslCert = self::escapeShellArgument($sslCert);
            $sslKey = self::escapeShellArgument($sslKey);

            $sslConfig = "\nserver {\n";
            $sslConfig .= "    listen 443 ssl;\n";
            $sslConfig .= "    server_name " . $domain . ";\n";
            $sslConfig .= "    root " . $root . ";\n";
            $sslConfig .= "\n";
            $sslConfig .= "    index index.html index.htm index.php;\n";
            $sslConfig .= "\n";
            $sslConfig .= "    ssl_certificate " . $sslCert . ";\n";
            $sslConfig .= "    ssl_certificate_key " . $sslKey . ";\n";
            $sslConfig .= "\n";
            $sslConfig .= "    location / {\n";
            $sslConfig .= "        try_files \$uri \$uri/ =404;\n";
            $sslConfig .= "    }\n";
            $sslConfig .= "}\n";
        }

        $config .= $sslConfig;

        $encoded = base64_encode($config);
        $path = self::escapeShellArgument(self::DEFAULT_AVAILABLE_DIR . '/' . $name);
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . $path;

        $result = $this->remoteExec($cmd);

        if ($result->isSuccessful()) {
            return $this->enableSite($name);
        }

        return $result;
    }

    /**
     * Delete an Nginx virtual host configuration.
     *
     * @param string $name Configuration name
     * @return RemoteCommandOutput
     */
    public function deleteVhost(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);
        $available = self::escapeShellArgument(self::DEFAULT_AVAILABLE_DIR);
        $enabled = self::escapeShellArgument(self::DEFAULT_ENABLED_DIR);

        return $this->remoteExec('rm -f ' . $available . '/' . $name . ' && rm -f ' . $enabled . '/' . $name);
    }

    /**
     * Get the last N lines of the Nginx access log.
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
     * Get the last N lines of the Nginx error log.
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
     * Start the Nginx service.
     *
     * @return RemoteCommandOutput
     */
    public function start(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl start nginx');
    }

    /**
     * Stop the Nginx service.
     *
     * @return RemoteCommandOutput
     */
    public function stop(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl stop nginx');
    }
}
