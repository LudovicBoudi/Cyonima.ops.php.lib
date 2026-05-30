<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * SUSE Linux operations
 *
 * Package management using zypper
 */
class SuseOps extends AbstractLinuxOps
{
    /**
     * Install a package using zypper
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("zypper install -y $package");
    }

    /**
     * Install a package with privilege elevation
     *
     * @param string $package Package name
     * @param string $sudoPassword Sudo password
     * @return string Command output
     */
    public function installPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S zypper install -y $package";
        return $this->remoteExec($cmd);
    }

    /**
     * Install a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return string Command output
     */
    public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("sudo zypper install -y $package");
    }

    /**
     * Uninstall a package
     *
     * @param string $package Package name
     * @return string Command output
     */
    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("zypper remove -y $package");
    }

    /**
     * Uninstall a package with privilege elevation
     *
     * @param string $package Package name
     * @param string $sudoPassword Sudo password
     * @return string Command output
     */
    public function uninstallPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S zypper remove -y $package";
        return $this->remoteExec($cmd);
    }

    /**
     * Uninstall a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return string Command output
     */
    public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("sudo zypper remove -y $package");
    }

    /**
     * Upgrade all packages
     *
     * @return string Command output
     */
    public function upgradePackages(): RemoteCommandOutput
    {
        $cmd = "zypper refresh && zypper update -y";
        return $this->remoteExec($cmd);
    }

    /**
     * Upgrade all packages with privilege elevation
     *
     * @param string $sudoPassword Sudo password
     * @return string Command output
     */
    public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S zypper refresh && sudo -S zypper update -y";
        return $this->remoteExec($cmd);
    }

    /**
     * Upgrade all packages with privilege elevation (no password required)
     *
     * @return string Command output
     */
    public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput
    {
        $cmd = "sudo zypper refresh && sudo zypper update -y";
        return $this->remoteExec($cmd);
    }
}
