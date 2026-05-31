<?php

declare(strict_types=1);

namespace Cyonima\Ops\Bsd;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for BSD operations
 *
 * Provides common FreeBSD/OpenBSD helpers for remote hosts via SSH.
 */
abstract class AbstractBsdOps extends AbstractOps
{
    /**
     * Return a friendly BSD distribution name
     */
    abstract public function getDistribution(): string;

    /**
     * Retrieve BSD version information
     */
    public function getBsdVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('uname -sr');
    }

    /**
     * Retrieve a service status
     */
    public function getService(string $serviceName): RemoteCommandOutput
    {
        return $this->remoteExec('service ' . self::escapeShellArgument($serviceName) . ' status');
    }

    /**
     * Manage a BSD service
     */
    public function manageService(string $serviceName, string $action): RemoteCommandOutput
    {
        $action = strtolower($action);
        if (!in_array($action, ['start', 'stop', 'restart', 'status'], true)) {
            throw new \InvalidArgumentException('Unsupported service action: ' . $action);
        }

        return $this->remoteExec('service ' . self::escapeShellArgument($serviceName) . ' ' . self::escapeShellArgument($action));
    }

    /**
     * Manage a BSD service with privilege elevation
     */
    public function manageServiceWithPrivilege(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    /**
     * Manage a BSD service with privilege elevation (no password required)
     */
    public function manageServiceWithPrivilegeNoPwd(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    /**
     * Read file contents
     */
    public function readFile(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('cat ' . self::escapeShellArgument($path));
    }

    /**
     * Write contents to a file
     */
    public function writeFile(string $path, string $content): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . self::escapeShellArgument($path);
        return $this->remoteExec($cmd);
    }

    /**
     * Write contents to a file with privilege elevation
     */
    public function writeFileWithPrivilege(string $path, string $content, string $sudoPassword): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . self::escapeShellArgument($path);
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    /**
     * Copy a file
     */
    public function copyFile(string $source, string $destination): RemoteCommandOutput
    {
        return $this->remoteExec('cp -p ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination));
    }

    /**
     * Move or rename a file
     */
    public function moveFile(string $source, string $destination): RemoteCommandOutput
    {
        return $this->remoteExec('mv ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination));
    }

    /**
     * Remove a file or directory
     */
    public function removeFile(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('rm -rf ' . self::escapeShellArgument($path));
    }

    /**
     * Create a directory (with parents)
     */
    public function makeDirectory(string $path, string $mode = '0755'): RemoteCommandOutput
    {
        return $this->remoteExec('mkdir -p -m ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path));
    }

    /**
     * Change file or directory permissions
     */
    public function changePermissions(string $path, string $mode): RemoteCommandOutput
    {
        return $this->remoteExec('chmod ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path));
    }

    /**
     * Change file or directory ownership
     */
    public function changeOwner(string $path, string $owner, ?string $group = null): RemoteCommandOutput
    {
        $target = self::escapeShellArgument($owner . ($group !== null ? ':' . $group : ''));
        return $this->remoteExec('chown ' . $target . ' ' . self::escapeShellArgument($path));
    }

    /**
     * Retrieve BSD network interfaces
     */
    public function getNetworkInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('ifconfig -a');
    }

    /**
     * Retrieve the routing table
     */
    public function getRoutingTable(): RemoteCommandOutput
    {
        return $this->remoteExec('netstat -rn');
    }

    /**
     * Add a network route
     */
    public function addNetworkRoute(string $destination, string $gateway, ?string $interface = null): RemoteCommandOutput
    {
        $cmd = 'route add -net ' . self::escapeShellArgument($destination) . ' ' . self::escapeShellArgument($gateway);
        if ($interface !== null && $interface !== '') {
            $cmd .= ' -iface ' . self::escapeShellArgument($interface);
        }
        return $this->executeWithSudo($cmd);
    }

    /**
     * Get BSD firewall status
     */
    public function getFirewallStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('pfctl -s info || true');
    }

    /**
     * Enable BSD firewall
     */
    public function enableFirewall(): RemoteCommandOutput
    {
        return $this->executeWithSudo('pfctl -e');
    }

    /**
     * Disable BSD firewall
     */
    public function disableFirewall(): RemoteCommandOutput
    {
        return $this->executeWithSudo('pfctl -d');
    }

    /**
     * Add a firewall rule
     */
    public function addFirewallRule(string $rule): RemoteCommandOutput
    {
        $cmd = 'echo ' . self::escapeShellArgument($rule) . ' | pfctl -f -';
        return $this->executeWithSudo($cmd);
    }

    /**
     * Add a new BSD user account
     */
    public function addUser(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        $escapedUser = self::escapeShellArgument($username);
        $escapedPassword = self::escapeShellArgument($username . ':' . $password);
        $script = 'if command -v pw >/dev/null 2>&1; then ' .
            'pw useradd -n ' . $escapedUser . ' -m -s /bin/sh && ' .
            'echo ' . $escapedPassword . ' | chpasswd ' .
            'elif command -v useradd >/dev/null 2>&1; then ' .
            'useradd -m -s /bin/sh ' . $escapedUser . ' && ' .
            'echo ' . $escapedPassword . ' | chpasswd ' .
            'else echo \'No supported user creation tool found.\' ; exit 1; fi';

        return $this->remoteExec($script);
    }

    /**
     * Delete a BSD user account
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        $escapedUser = self::escapeShellArgument($username);
        $script = 'if command -v pw >/dev/null 2>&1; then ' .
            'pw userdel -n ' . $escapedUser . ' -r ' .
            'elif command -v userdel >/dev/null 2>&1; then ' .
            'userdel -r ' . $escapedUser . ' ' .
            'else echo \'No supported user deletion tool found.\' ; exit 1; fi';

        return $this->remoteExec($script);
    }

    /**
     * Change a BSD user password
     */
    public function changePassword(string $username, string $newPassword): RemoteCommandOutput
    {
        $escapedUser = self::escapeShellArgument($username);
        $escapedPassword = self::escapeShellArgument($newPassword);
        $script = 'if command -v chpasswd >/dev/null 2>&1; then ' .
            'echo ' . self::escapeShellArgument($username . ':' . $newPassword) . ' | chpasswd ' .
            'elif command -v passwd >/dev/null 2>&1; then ' .
            'printf \'%s\\n%s\\n\' ' . $escapedPassword . ' ' . $escapedPassword . ' | passwd ' . $escapedUser . ' ' .
            'else echo \'No supported password management tool found.\' ; exit 1; fi';

        return $this->remoteExec($script);
    }

    /**
     * Execute a command with sudo
     */
    protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput
    {
        if ($sudoPassword !== null) {
            $command = 'echo ' . self::escapeShellArgument($sudoPassword) . ' | sudo -S sh -lc ' . self::escapeShellArgument($command);
        } else {
            $command = 'sudo sh -lc ' . self::escapeShellArgument($command);
        }
        return $this->remoteExec($command);
    }

    abstract public function installPackage(string $package): RemoteCommandOutput;
    abstract public function installPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput;
    abstract public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput;
    abstract public function uninstallPackage(string $package): RemoteCommandOutput;
    abstract public function uninstallPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput;
    abstract public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput;
    abstract public function upgradePackages(): RemoteCommandOutput;
    abstract public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput;
    abstract public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput;
}
