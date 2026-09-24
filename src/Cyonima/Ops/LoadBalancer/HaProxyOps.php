<?php

declare(strict_types=1);

namespace Cyonima\Ops\LoadBalancer;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * HAProxy load balancer operations over SSH
 *
 * Provides a direct interface to manage HAProxy configuration, service
 * lifecycle, and runtime administration via socat and systemctl.
 *
 * Example usage:
 * ```php
 * $haproxy = new HaProxyOps();
 * $haproxy->setHost('lb.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $haproxy->openConnection();
 * $haproxy->reload();
 * $haproxy->showStat();
 * $haproxy->enableServer('web-backend', 'web-01');
 * $haproxy->closeConnection();
 * ```
 */
class HaProxyOps extends AbstractOps
{
    public const string DEFAULT_CONFIG_FILE = '/etc/haproxy/haproxy.cfg';
    public const string DEFAULT_SOCKET = '/var/run/haproxy/admin.sock';

    /**
     * Get the installed HAProxy version.
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('haproxy -v');
    }

    /**
     * Get the current status of the HAProxy service.
     *
     * @return RemoteCommandOutput
     */
    public function getStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl is-active haproxy');
    }

    /**
     * Reload HAProxy configuration gracefully.
     *
     * @return RemoteCommandOutput
     */
    public function reload(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl reload haproxy');
    }

    /**
     * Restart the HAProxy service.
     *
     * @return RemoteCommandOutput
     */
    public function restart(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl restart haproxy');
    }

    /**
     * Start the HAProxy service.
     *
     * @return RemoteCommandOutput
     */
    public function start(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl start haproxy');
    }

    /**
     * Stop the HAProxy service.
     *
     * @return RemoteCommandOutput
     */
    public function stop(): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl stop haproxy');
    }

    /**
     * Test HAProxy configuration file for syntax errors.
     *
     * @param string|null $configFile Path to the configuration file (default: DEFAULT_CONFIG_FILE)
     * @return RemoteCommandOutput
     */
    public function testConfig(?string $configFile = null): RemoteCommandOutput
    {
        $configFile = self::escapeShellArgument($configFile ?? self::DEFAULT_CONFIG_FILE);
        return $this->remoteExec('haproxy -c -f ' . $configFile);
    }

    /**
     * Show HAProxy statistics via the admin socket.
     *
     * @param string|null $socket Path to the HAProxy stats socket (default: DEFAULT_SOCKET)
     * @return RemoteCommandOutput
     */
    public function showStat(?string $socket = null): RemoteCommandOutput
    {
        $socket = self::escapeShellArgument($socket ?? self::DEFAULT_SOCKET);
        return $this->remoteExec("echo 'show stat' | socat {$socket} -");
    }

    /**
     * Show HAProxy info via the admin socket.
     *
     * @param string|null $socket Path to the HAProxy stats socket (default: DEFAULT_SOCKET)
     * @return RemoteCommandOutput
     */
    public function showInfo(?string $socket = null): RemoteCommandOutput
    {
        $socket = self::escapeShellArgument($socket ?? self::DEFAULT_SOCKET);
        return $this->remoteExec("echo 'show info' | socat {$socket} -");
    }

    /**
     * Enable a server in a backend via the admin socket.
     *
     * @param string $backend Backend name
     * @param string $server Server name
     * @param string|null $socket Path to the HAProxy stats socket (default: DEFAULT_SOCKET)
     * @return RemoteCommandOutput
     */
    public function enableServer(string $backend, string $server, ?string $socket = null): RemoteCommandOutput
    {
        $socket = self::escapeShellArgument($socket ?? self::DEFAULT_SOCKET);
        $backend = self::escapeShellArgument($backend);
        $server = self::escapeShellArgument($server);
        $echoArg = self::escapeShellArgument("enable server {$backend}/{$server}");
        return $this->remoteExec("echo {$echoArg} | socat {$socket} -");
    }

    /**
     * Disable a server in a backend via the admin socket.
     *
     * @param string $backend Backend name
     * @param string $server Server name
     * @param string|null $socket Path to the HAProxy stats socket (default: DEFAULT_SOCKET)
     * @return RemoteCommandOutput
     */
    public function disableServer(string $backend, string $server, ?string $socket = null): RemoteCommandOutput
    {
        $socket = self::escapeShellArgument($socket ?? self::DEFAULT_SOCKET);
        $backend = self::escapeShellArgument($backend);
        $server = self::escapeShellArgument($server);
        $echoArg = self::escapeShellArgument("disable server {$backend}/{$server}");
        return $this->remoteExec("echo {$echoArg} | socat {$socket} -");
    }

    /**
     * Set the weight of a server in a backend via the admin socket.
     *
     * @param string $backend Backend name
     * @param string $server Server name
     * @param int $weight Server weight value
     * @param string|null $socket Path to the HAProxy stats socket (default: DEFAULT_SOCKET)
     * @return RemoteCommandOutput
     */
    public function setServerWeight(string $backend, string $server, int $weight, ?string $socket = null): RemoteCommandOutput
    {
        $socket = self::escapeShellArgument($socket ?? self::DEFAULT_SOCKET);
        $backend = self::escapeShellArgument($backend);
        $server = self::escapeShellArgument($server);
        $echoArg = self::escapeShellArgument("set weight {$backend}/{$server} {$weight}");
        return $this->remoteExec("echo {$echoArg} | socat {$socket} -");
    }

    /**
     * Get the contents of the HAProxy configuration file.
     *
     * @param string|null $configFile Path to the configuration file (default: DEFAULT_CONFIG_FILE)
     * @return RemoteCommandOutput
     */
    public function getConfig(?string $configFile = null): RemoteCommandOutput
    {
        $configFile = self::escapeShellArgument($configFile ?? self::DEFAULT_CONFIG_FILE);
        return $this->remoteExec('cat ' . $configFile);
    }

    /**
     * List all backends via the admin socket.
     *
     * @param string|null $socket Path to the HAProxy stats socket (default: DEFAULT_SOCKET)
     * @return RemoteCommandOutput
     */
    public function getBackends(?string $socket = null): RemoteCommandOutput
    {
        $socket = self::escapeShellArgument($socket ?? self::DEFAULT_SOCKET);
        return $this->remoteExec("echo 'show backend' | socat {$socket} -");
    }

    /**
     * List all frontends via the admin socket.
     *
     * @param string|null $socket Path to the HAProxy stats socket (default: DEFAULT_SOCKET)
     * @return RemoteCommandOutput
     */
    public function getFrontends(?string $socket = null): RemoteCommandOutput
    {
        $socket = self::escapeShellArgument($socket ?? self::DEFAULT_SOCKET);
        return $this->remoteExec("echo 'show frontend' | socat {$socket} -");
    }
}
