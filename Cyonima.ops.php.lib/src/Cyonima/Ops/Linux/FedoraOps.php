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
     * @return RemoteCommandOutput
     */
    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('dnf install -y ' . self::escapeShellArgument($package));
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
        return $this->executeWithSudo('dnf install -y ' . self::escapeShellArgument($package), $sudoPassword);
    }

    /**
     * Install a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->executeWithSudo('dnf install -y ' . self::escapeShellArgument($package));
    }

    /**
     * Uninstall a package
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('dnf remove -y ' . self::escapeShellArgument($package));
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
        return $this->executeWithSudo('dnf remove -y ' . self::escapeShellArgument($package), $sudoPassword);
    }

    /**
     * Uninstall a package with privilege elevation (no password required)
     *
     * @param string $package Package name
     * @return RemoteCommandOutput
     */
    public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->executeWithSudo('dnf remove -y ' . self::escapeShellArgument($package));
    }

    /**
     * Upgrade all packages
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackages(): RemoteCommandOutput
    {
        return $this->remoteExec('dnf upgrade --refresh -y');
    }

    /**
     * Upgrade all packages with privilege elevation
     *
     * @param string $sudoPassword Sudo password
     * @return RemoteCommandOutput
     */
    public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('dnf upgrade --refresh -y', $sudoPassword);
    }

    /**
     * Upgrade all packages with privilege elevation (no password required)
     *
     * @return RemoteCommandOutput
     */
    public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput
    {
        return $this->executeWithSudo('dnf upgrade --refresh -y');
    }
}
