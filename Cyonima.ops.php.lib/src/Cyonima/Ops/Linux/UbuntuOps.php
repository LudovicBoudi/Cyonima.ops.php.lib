<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Ubuntu Linux operations
 *
 * Package management using apt
 */
class UbuntuOps extends AbstractLinuxOps
{
    /**
     * Install a package using apt
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("apt install -y $package");
    }

    /**
     * Install a package with privilege elevation
     *
     * @param string $package Package name
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function installPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S apt install -y $package";
        return $this->remoteExec($cmd);
    }

    /**
     * Install a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("sudo apt install -y $package");
    }

    /**
     * Uninstall a package
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("apt remove -y $package");
    }

    /**
     * Uninstall a package with privilege elevation
     *
     * @param string $package Package name
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function uninstallPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S apt remove -y $package";
        return $this->remoteExec($cmd);
    }

    /**
     * Uninstall a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("sudo apt remove -y $package");
    }

    /**
     * Upgrade all packages
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackages(): RemoteCommandOutput
    {
        $cmd = "apt update && apt upgrade -y";
        return $this->remoteExec($cmd);
    }

    /**
     * Upgrade all packages with privilege elevation
     *
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S apt update && sudo -S apt upgrade -y";
        return $this->remoteExec($cmd);
    }

    /**
     * Upgrade all packages with privilege elevation (no password required)
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput
    {
        $cmd = "sudo apt update && sudo apt upgrade -y";
