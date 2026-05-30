<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Fedora Linux operations
 *
 * Package management using dnf
 */
class FedoraOps extends AbstractLinuxOps
{
    /**
     * Install a package using dnf
     *
     * @param string $package Package name
     * @return string Command output
     */
    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("dnf install -y $package");
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
        $cmd = "echo $sudoPassword | sudo -S dnf install -y $package";
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
        return $this->remoteExec("sudo dnf install -y $package");
    }

    /**
     * Uninstall a package
     *
     * @param string $package Package name
     * @return string Command output
     */
    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("dnf remove -y $package");
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
        $cmd = "echo $sudoPassword | sudo -S dnf remove -y $package";
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
        return $this->remoteExec("sudo dnf remove -y $package");
    }

    /**
     * Upgrade all packages
     *
     * @return string Command output
     */
    public function upgradePackages(): RemoteCommandOutput
    {
        return $this->remoteExec("dnf upgrade --refresh -y");
    }

    /**
     * Upgrade all packages with privilege elevation
     *
     * @param string $sudoPassword Sudo password
     * @return string Command output
     */
    public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S dnf upgrade --refresh -y";
        return $this->remoteExec($cmd);
    }

    /**
     * Upgrade all packages with privilege elevation (no password required)
     *
     * @return string Command output
     */
    public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput
    {
        return $this->remoteExec("sudo dnf upgrade --refresh -y");
    }
}
