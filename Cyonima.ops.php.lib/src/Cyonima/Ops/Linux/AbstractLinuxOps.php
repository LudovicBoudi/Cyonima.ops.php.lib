<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for Linux operations
 *
 * Provides common methods for managing Linux systems
 */
abstract class AbstractLinuxOps extends AbstractOps
{
    /**
     * Control a systemd service
     *
     * @param string $service Service name
     * @param string $action Action (start, stop, restart, status, etc.)
     * @return RemoteCommandOutput
     */
    public function systemctl(string $service, string $action): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl ' . self::escapeShellArgument($action) . ' ' . self::escapeShellArgument($service));
    }

    /**
     * Control a systemd service with privilege elevation
     *
     * @param string $service Service name
     * @param string $action Action (start, stop, restart, status, etc.)
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function systemctlWithPrivilege(string $service, string $action, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('systemctl ' . self::escapeShellArgument($action) . ' ' . self::escapeShellArgument($service), $sudoPassword);
    }

    /**
     * Control a systemd service with privilege elevation (no password required)
     *
     * @param string $service Service name
     * @param string $action Action (start, stop, restart, status, etc.)
     * @return RemoteCommandOutput
     */
    public function systemctlWithPrivilegeNoPwd(string $service, string $action): RemoteCommandOutput
    {
        return $this->executeWithSudo('systemctl ' . self::escapeShellArgument($action) . ' ' . self::escapeShellArgument($service));
    }

    /**
     * Add a new user account
     *
     * @param string $username Username
     * @param string $group Primary group
     * @param string $password User password
     * @return RemoteCommandOutput
     */
    public function addUser(string $username, string $group, string $password): RemoteCommandOutput
    {
        $cmd = 'adduser -m -s /bin/bash ' . self::escapeShellArgument($group) . ' ' . self::escapeShellArgument($username);
        $cmd .= ' && echo ' . self::escapeShellArgument($password) . ' | sudo passwd ' . self::escapeShellArgument($username);
        return $this->remoteExec($cmd);
    }

    /**
     * Add a new user with privilege elevation
     *
     * @param string $username Username
     * @param string $password User password
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function addUserWithPrivilege(string $username, string $password, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'useradd -m -s /bin/bash ' . self::escapeShellArgument($username) . ' && echo ' . self::escapeShellArgument($username . ':' . $password) . ' | chpasswd';
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    /**
     * Delete a user account
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        return $this->executeWithSudo('userdel -r ' . self::escapeShellArgument($username));
    }

    /**
     * Delete a user with privilege elevation
     *
     * @param string $username Username
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function deleteUserWithPrivilege(string $username, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('userdel -r ' . self::escapeShellArgument($username), $sudoPassword);
    }

    /**
     * Change user password
     *
     * @param string $username Username
     * @param string $newPassword New password
     * @return RemoteCommandOutput
     */
    public function changePassword(string $username, string $newPassword): RemoteCommandOutput
    {
        $cmd = 'echo ' . self::escapeShellArgument($username . ':' . $newPassword) . ' | chpasswd';
        return $this->remoteExec($cmd);
    }

    /**
     * Change user password with privilege elevation
     *
     * @param string $username Username
     * @param string $newPassword New password
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function changePasswordWithPrivilege(string $username, string $newPassword, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'echo ' . self::escapeShellArgument($username . ':' . $newPassword) . ' | chpasswd';
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    /**
     * Read file contents
     *
     * @param string $path File path
     * @return RemoteCommandOutput
     */
    public function readFile(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('cat ' . self::escapeShellArgument($path));
    }

    /**
     * Write contents to a file
     *
     * @param string $path File path
     * @param string $content File content
     * @return RemoteCommandOutput
     */
    public function writeFile(string $path, string $content): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . self::escapeShellArgument($path);
        return $this->remoteExec($cmd);
    }

    /**
     * Write contents to a file with privilege elevation
     *
     * @param string $path File path
     * @param string $content File content
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function writeFileWithPrivilege(string $path, string $content, string $sudoPassword): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . self::escapeShellArgument($path);
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    /**
     * Copy a file
     *
     * @param string $source Source path
     * @param string $destination Destination path
     * @return RemoteCommandOutput
     */
    public function copyFile(string $source, string $destination): RemoteCommandOutput
    {
        return $this->remoteExec('cp -p ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination));
    }

    /**
     * Copy a file with privilege elevation
     *
     * @param string $source Source path
     * @param string $destination Destination path
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function copyFileWithPrivilege(string $source, string $destination, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'cp -p ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination);
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    /**
     * Move or rename a file
     *
     * @param string $source Source path
     * @param string $destination Destination path
     * @return RemoteCommandOutput
     */
    public function moveFile(string $source, string $destination): RemoteCommandOutput
    {
        return $this->remoteExec('mv ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination));
    }

    /**
     * Move or rename a file with privilege elevation
     *
     * @param string $source Source path
     * @param string $destination Destination path
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function moveFileWithPrivilege(string $source, string $destination, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'mv ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination);
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    /**
     * Remove a file or directory
     *
     * @param string $path Path to remove
     * @return RemoteCommandOutput
     */
    public function removeFile(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('rm -rf ' . self::escapeShellArgument($path));
    }

    /**
     * Remove a file or directory with privilege elevation
     *
     * @param string $path Path to remove
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function removeFileWithPrivilege(string $path, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('rm -rf ' . self::escapeShellArgument($path), $sudoPassword);
    }

    /**
     * Create a directory (with parents)
     *
     * @param string $path Directory path
     * @param string $mode Directory mode
     * @return RemoteCommandOutput
     */
    public function makeDirectory(string $path, string $mode = '0755'): RemoteCommandOutput
    {
        return $this->remoteExec('mkdir -p -m ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path));
    }

    /**
     * Create a directory with privilege elevation
     *
     * @param string $path Directory path
     * @param string $mode Directory mode
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function makeDirectoryWithPrivilege(string $path, string $mode, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('mkdir -p -m ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path), $sudoPassword);
    }

    /**
     * Change file or directory permissions
     *
     * @param string $path File path
     * @param string $mode Permissions mode
     * @return RemoteCommandOutput
     */
    public function changePermissions(string $path, string $mode): RemoteCommandOutput
    {
        return $this->remoteExec('chmod ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path));
    }

    /**
     * Change file or directory permissions with privilege elevation
     *
     * @param string $path File path
     * @param string $mode Permissions mode
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function changePermissionsWithPrivilege(string $path, string $mode, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('chmod ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path), $sudoPassword);
    }

    /**
     * Change file or directory owner
     *
     * @param string $path File path
     * @param string $owner Owner name
     * @param string|null $group Optional group name
     * @return RemoteCommandOutput
     */
    public function changeOwner(string $path, string $owner, ?string $group = null): RemoteCommandOutput
    {
        $target = self::escapeShellArgument($owner . ($group !== null ? ':' . $group : ''));
        return $this->remoteExec('chown ' . $target . ' ' . self::escapeShellArgument($path));
    }

    /**
     * Change file or directory owner with privilege elevation
     *
     * @param string $path File path
     * @param string $owner Owner name
     * @param string|null $group Optional group name
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function changeOwnerWithPrivilege(string $path, string $owner, ?string $group, string $sudoPassword): RemoteCommandOutput
    {
        $target = self::escapeShellArgument($owner . ($group !== null ? ':' . $group : ''));
        return $this->executeWithSudo('chown ' . $target . ' ' . self::escapeShellArgument($path), $sudoPassword);
    }

    /**
     * List running processes optionally filtered by a pattern
     *
     * @param string $filter Optional filter pattern
     * @return RemoteCommandOutput
     */
    public function listProcesses(string $filter = ''): RemoteCommandOutput
    {
        if ($filter === '') {
            return $this->remoteExec('ps -ef');
        }

        return $this->remoteExec('ps -ef | grep -E ' . self::escapeShellArgument($filter) . ' | grep -v grep || true');
    }

    /**
     * Kill a process by PID
     *
     * @param int $pid Process ID
     * @return RemoteCommandOutput
     */
    public function killProcess(int $pid): RemoteCommandOutput
    {
        return $this->remoteExec('kill -15 ' . self::escapeShellArgument((string) $pid));
    }

    /**
     * Kill a process by name
     *
     * @param string $name Process name or pattern
     * @return RemoteCommandOutput
     */
    public function killProcessByName(string $name): RemoteCommandOutput
    {
        return $this->remoteExec('pkill -f ' . self::escapeShellArgument($name));
    }

    /**
     * Get the top CPU-consuming processes
     *
     * @param int $count Number of lines
     * @return RemoteCommandOutput
     */
    public function getTopProcesses(int $count = 10): RemoteCommandOutput
    {
        return $this->remoteExec('ps -eo pid,comm,%cpu,%mem --sort=-%cpu | head -n ' . self::escapeShellArgument((string) $count));
    }

    /**
     * Read a log file
     *
     * @param string $path Log file path
     * @param int $lines Number of tail lines
     * @return RemoteCommandOutput
     */
    public function readLogFile(string $path, int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('tail -n ' . self::escapeShellArgument((string) $lines) . ' ' . self::escapeShellArgument($path));
    }

    /**
     * Read a systemd journal unit
     *
     * @param string $unit Systemd unit name
     * @param int $lines Number of lines
     * @return RemoteCommandOutput
     */
    public function readJournalLog(string $unit, int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('journalctl -u ' . self::escapeShellArgument($unit) . ' -n ' . self::escapeShellArgument((string) $lines) . ' --no-pager');
    }

    /**
     * Tail a systemd journal unit
     *
     * @param string $unit Systemd unit name
     * @param int $lines Number of lines
     * @return RemoteCommandOutput
     */
    public function tailJournalLog(string $unit, int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('journalctl -u ' . self::escapeShellArgument($unit) . ' -n ' . self::escapeShellArgument((string) $lines) . ' --follow --no-pager');
    }

    /**
     * List network interfaces
     *
     * @return RemoteCommandOutput
     */
    public function getNetworkInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('ip addr show || ifconfig -a');
    }

    /**
     * Get the IP routing table
     *
     * @return RemoteCommandOutput
     */
    public function getRoutingTable(): RemoteCommandOutput
    {
        return $this->remoteExec('ip route show || netstat -rn');
    }

    /**
     * Add a persistent network route
     *
     * @param string $destination Destination prefix
     * @param string $gateway Gateway IP address
     * @param string|null $device Optional device name
     * @return RemoteCommandOutput
     */
    public function addNetworkRoute(string $destination, string $gateway, ?string $device = null): RemoteCommandOutput
    {
        $cmd = 'ip route add ' . self::escapeShellArgument($destination) . ' via ' . self::escapeShellArgument($gateway);
        if ($device !== null && $device !== '') {
            $cmd .= ' dev ' . self::escapeShellArgument($device);
        }
        return $this->remoteExec($cmd);
    }

    /**
     * Get the current firewall status
     *
     * @return RemoteCommandOutput
     */
    public function getFirewallStatus(): RemoteCommandOutput
    {
        $script = <<<'BASH'
if command -v ufw >/dev/null 2>&1; then
  ufw status verbose
elif command -v firewall-cmd >/dev/null 2>&1; then
  firewall-cmd --state
else
  echo 'No supported firewall manager found.'
  exit 1
fi
BASH;
        return $this->remoteExec($script);
    }

    /**
     * Enable the firewall
     *
     * @return RemoteCommandOutput
     */
    public function enableFirewall(): RemoteCommandOutput
    {
        $script = <<<'BASH'
if command -v ufw >/dev/null 2>&1; then
  ufw --force enable
elif command -v firewall-cmd >/dev/null 2>&1; then
  firewall-cmd --set-default-zone=public
  firewall-cmd --reload
else
  echo 'No supported firewall manager found.'
  exit 1
fi
BASH;
        return $this->executeWithSudo($script);
    }

    /**
     * Disable the firewall
     *
     * @return RemoteCommandOutput
     */
    public function disableFirewall(): RemoteCommandOutput
    {
        $script = <<<'BASH'
if command -v ufw >/dev/null 2>&1; then
  ufw disable
elif command -v firewall-cmd >/dev/null 2>&1; then
  firewall-cmd --panic-off
  firewall-cmd --reload
else
  echo 'No supported firewall manager found.'
  exit 1
fi
BASH;
        return $this->executeWithSudo($script);
    }

    /**
     * Add a firewall rule for a port/protocol
     *
     * @param string $port Port number or range
     * @param string $protocol Network protocol (tcp|udp)
     * @return RemoteCommandOutput
     */
    public function addFirewallRule(string $port, string $protocol = 'tcp'): RemoteCommandOutput
    {
        $script = 'if command -v ufw >/dev/null 2>&1; then ' .
            'ufw allow ' . self::escapeShellArgument($port . '/' . $protocol) . '; ' .
            'elif command -v firewall-cmd >/dev/null 2>&1; then ' .
            'firewall-cmd --permanent --add-port=' . self::escapeShellArgument($port . '/' . $protocol) . ' && firewall-cmd --reload; ' .
            'else echo "No supported firewall manager found."; exit 1; fi';

        return $this->executeWithSudo($script);
    }

    /**
     * Get SELinux status
     *
     * @return RemoteCommandOutput
     */
    public function getSelinuxStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('getenforce || true');
    }

    /**
     * Set SELinux mode
     *
     * @param string $mode Target mode (Enforcing, Permissive, Disabled)
     * @return RemoteCommandOutput
     */
    public function setSelinuxMode(string $mode): RemoteCommandOutput
    {
        return $this->executeWithSudo('setenforce ' . self::escapeShellArgument($mode));
    }

    /**
     * Get AppArmor status
     *
     * @return RemoteCommandOutput
     */
    public function getAppArmorStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('apparmor_status || true');
    }

    /**
     * Enforce an AppArmor profile
     *
     * @param string $profile AppArmor profile name
     * @return RemoteCommandOutput
     */
    public function enforceAppArmorProfile(string $profile): RemoteCommandOutput
    {
        return $this->executeWithSudo('aa-enforce ' . self::escapeShellArgument($profile));
    }

    /**
     * Install a package
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    abstract public function installPackage(string $package): RemoteCommandOutput;

    /**
     * Install a package with privilege elevation
     *
     * @param string $package Package name
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    abstract public function installPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput;

    /**
     * Install a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    abstract public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput;

    /**
     * Uninstall a package
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    abstract public function uninstallPackage(string $package): RemoteCommandOutput;

    /**
     * Uninstall a package with privilege elevation
     *
     * @param string $package Package name
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    abstract public function uninstallPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput;

    /**
     * Uninstall a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    abstract public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput;

    /**
     * Upgrade all packages
     *
     * @return RemoteCommandOutput
     */
    abstract public function upgradePackages(): RemoteCommandOutput;

    /**
     * Upgrade all packages with privilege elevation
     *
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    abstract public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput;

    /**
     * Upgrade all packages with privilege elevation (no password required)
     *
     * @return RemoteCommandOutput
     */
    abstract public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput;

    /**
     * Execute a command under sudo, with optional password
     *
     * @param string $command Command to execute
     * @param string|null $sudoPassword Optional sudo password
     * @return RemoteCommandOutput
     */
    protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput
    {
        if ($sudoPassword !== null) {
            $command = 'echo ' . self::escapeShellArgument($sudoPassword) . ' | sudo -S bash -lc ' . self::escapeShellArgument($command);
        } else {
            $command = 'sudo bash -lc ' . self::escapeShellArgument($command);
        }

        return $this->remoteExec($command);
    }
}
