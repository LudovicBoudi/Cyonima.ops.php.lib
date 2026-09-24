<?php

declare(strict_types=1);

namespace Cyonima\Ops\MacOS;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for macOS operations
 *
 * Provides common macOS management helpers for remote hosts via SSH.
 */
abstract class AbstractMacOsOps extends AbstractOps
{
    /**
     * Retrieve macOS version information
     *
     * @return RemoteCommandOutput
     */
    public function getMacOsVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('sw_vers -productVersion');
    }

    /**
     * Get a service status using Homebrew services
     *
     * @param string $serviceName
     * @return RemoteCommandOutput
     */
    public function getService(string $serviceName): RemoteCommandOutput
    {
        $cmd = 'brew services list | grep -E "^' . $serviceName . '\\s" || true';
        return $this->remoteExec($cmd);
    }

    /**
     * Manage a Homebrew service on macOS
     *
     * @param string $serviceName
     * @param string $action (start|stop|restart|status)
     * @return RemoteCommandOutput
     */
    public function manageService(string $serviceName, string $action): RemoteCommandOutput
    {
        $action = strtolower($action);

        if ($action === 'status') {
            $cmd = 'brew services list | grep -E "^' . $serviceName . '\\s" || true';
        } elseif (in_array($action, ['start', 'stop', 'restart'], true)) {
            $cmd = 'brew services ' . self::escapeShellArgument($action) . ' ' . self::escapeShellArgument($serviceName);
        } else {
            throw new \InvalidArgumentException('Unsupported service action: ' . $action);
        }

        return $this->remoteExec($cmd);
    }

    /**
     * Manage a service with privilege elevation
     *
     * @param string $serviceName
     * @param string $action
     * @return RemoteCommandOutput
     */
    public function manageServiceWithPrivilege(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    /**
     * Manage a service with privilege elevation (no password required)
     *
     * @param string $serviceName
     * @param string $action
     * @return RemoteCommandOutput
     */
    public function manageServiceWithPrivilegeNoPwd(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    /**
     * Add a new local macOS user account
     *
     * @param string $username
     * @param string $password
     * @param string|null $group Optional group name, use 'admin' to create an administrator account
     * @return RemoteCommandOutput
     */
    public function addUser(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        $cmd = 'sudo sysadminctl -addUser ' . self::escapeShellArgument($username) .
            ' -password ' . self::escapeShellArgument($password) .
            ' -home /Users/' . self::escapeShellArgument($username) .
            ' -admin';

        if ($group !== null && trim(strtolower($group)) !== 'admin') {
            $cmd .= ' && sudo dscl . -append /Groups/' . self::escapeShellArgument($group) . ' GroupMembership ' . self::escapeShellArgument($username);
        }

        return $this->remoteExec($cmd);
    }

    /**
     * Add a new local macOS user account with privilege elevation
     *
     * @param string $username
     * @param string $password
     * @param string|null $group Optional group name
     * @return RemoteCommandOutput
     */
    public function addUserWithPrivilege(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        return $this->addUser($username, $password, $group);
    }

    /**
     * Delete a local macOS user account
     *
     * @param string $username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        $cmd = 'sudo sysadminctl -deleteUser ' . self::escapeShellArgument($username);
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a local macOS user account with privilege elevation
     *
     * @param string $username
     * @return RemoteCommandOutput
     */
    public function deleteUserWithPrivilege(string $username): RemoteCommandOutput
    {
        return $this->deleteUser($username);
    }

    /**
     * Change a local macOS user password
     *
     * @param string $username
     * @param string $newPassword
     * @return RemoteCommandOutput
     */
    public function changePassword(string $username, string $newPassword): RemoteCommandOutput
    {
        $cmd = 'sudo sysadminctl -resetPasswordFor ' . self::escapeShellArgument($username) .
            ' -newPassword ' . self::escapeShellArgument($newPassword);
        return $this->remoteExec($cmd);
    }

    /**
     * Change a local macOS user password with privilege elevation
     *
     * @param string $username
     * @param string $newPassword
     * @return RemoteCommandOutput
     */
    public function changePasswordWithPrivilege(string $username, string $newPassword): RemoteCommandOutput
    {
        return $this->changePassword($username, $newPassword);
    }

    /**
     * Install a package using Homebrew
     *
     * @param string $package
     * @return RemoteCommandOutput
     */
    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('brew install ' . self::escapeShellArgument($package));
    }

    /**
     * Install a package with privilege elevation
     *
     * @param string $package
     * @return RemoteCommandOutput
     */
    public function installPackageWithPrivilege(string $package): RemoteCommandOutput
    {
        return $this->installPackage($package);
    }

    /**
     * Uninstall a package using Homebrew
     *
     * @param string $package
     * @return RemoteCommandOutput
     */
    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('brew uninstall ' . self::escapeShellArgument($package));
    }

    /**
     * Uninstall a package with privilege elevation
     *
     * @param string $package
     * @return RemoteCommandOutput
     */
    public function uninstallPackageWithPrivilege(string $package): RemoteCommandOutput
    {
        return $this->uninstallPackage($package);
    }

    /**
     * Upgrade all installed Homebrew packages
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackages(): RemoteCommandOutput
    {
        return $this->remoteExec('brew update && brew upgrade');
    }

    /**
     * Upgrade all installed Homebrew packages with privilege elevation
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackagesWithPrivilege(): RemoteCommandOutput
    {
        return $this->upgradePackages();
    }
}
