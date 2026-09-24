<?php

declare(strict_types=1);

namespace Cyonima\Ops\Bsd;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * OpenBSD operations
 *
 * Package management uses `pkg_add`/`pkg_delete` and service control uses `service`.
 */
class OpenBsdOps extends AbstractBsdOps
{
    public function getDistribution(): string
    {
        return 'OpenBSD';
    }

    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('pkg_add ' . self::escapeShellArgument($package));
    }

    public function installPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg_add ' . self::escapeShellArgument($package), $sudoPassword);
    }

    public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg_add ' . self::escapeShellArgument($package));
    }

    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('pkg_delete ' . self::escapeShellArgument($package));
    }

    public function uninstallPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg_delete ' . self::escapeShellArgument($package), $sudoPassword);
    }

    public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg_delete ' . self::escapeShellArgument($package));
    }

    public function upgradePackages(): RemoteCommandOutput
    {
        return $this->remoteExec('pkg_add -u');
    }

    public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg_add -u', $sudoPassword);
    }

    public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg_add -u');
    }
}
