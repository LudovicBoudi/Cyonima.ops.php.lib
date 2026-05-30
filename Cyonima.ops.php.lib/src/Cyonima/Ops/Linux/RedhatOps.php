<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Red Hat Linux operations
 *
 * Package management using yum
 */
class RedhatOps extends AbstractLinuxOps
{
    /**
     * Install a package using yum
     *
     * @param string $package Package name
     * @return string Command output
     */
    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("yum install -y $package");
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
        $cmd = "echo $sudoPassword | sudo -S yum install -y $package";
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
        return $this->remoteExec("sudo yum install -y $package");
    }

    /**
     * Uninstall a package
     *
     * @param string $package Package name
     * @return string Command output
     */
    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec("yum remove -y $package");
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
        $cmd = "echo $sudoPassword | sudo -S yum remove -y $package";
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
        return $this->remoteExec("sudo yum remove -y $package");
    }

    /**
     * Upgrade all packages
     *
     * @return string Command output
     */
    public function upgradePackages(): RemoteCommandOutput
    {
        return $this->remoteExec("yum upgrade -y");
    }

    /**
     * Upgrade all packages with privilege elevation
     *
     * @param string $sudoPassword Sudo password
     * @return string Command output
     */
    public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput
    {
        $cmd = "echo $sudoPassword | sudo -S yum upgrade -y";
        return $this->remoteExec($cmd);
    }

    /**
     * Upgrade all packages with privilege elevation (no password required)
     *
     * @return string Command output
     */
    public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput
    {
        return $this->remoteExec("sudo yum upgrade -y");
    }
}
