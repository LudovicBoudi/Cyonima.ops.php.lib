<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;
use Cyonima\Ops\RemoteCommandOutput;

use Cyonima\Ops\AbstractOps;

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
        return $this->remoteExec("systemctl $action $service");
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
        $cmd = "echo $sudoPassword | sudo -S systemctl $action $service";
        return $this->remoteExec($cmd);
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
        return $this->remoteExec("sudo systemctl $action $service");
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
        $cmd = "adduser -m -s /bin/bash $group $username\n";
        $cmd .= "echo $password | sudo passwd $username\n";
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
        $cmd = "echo $sudoPassword | sudo -S useradd -m -s /bin/bash $username && echo '$username:$password' | sudo chpasswd";
        return $this->remoteExec($cmd);
    }

    /**
     * Delete a user account
     *
     * @param string $username Username
     * @return RemoteCommandOutput
     */
    public function deleteUser(string $username): RemoteCommandOutput
    {
        return $this->remoteExec("sudo userdel -r $username");
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
        $cmd = "echo $sudoPassword | sudo -S userdel -r $username";
        return $this->remoteExec($cmd);
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
        $cmd = "echo '$username:$newPassword' | chpasswd";
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
        $cmd = "echo $sudoPassword | sudo -S bash -c \"echo '$username:$newPassword' | chpasswd\"";
        return $this->remoteExec($cmd);
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
}
