<?php

declare(strict_types=1);

namespace Cyonima\Ops\Bsd;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * FreeBSD operations
 *
 * Package management uses `pkg` and service control uses `service`.
 */
class FreeBsdOps extends AbstractBsdOps
{
    public function getDistribution(): string
    {
        return 'FreeBSD';
    }

    public function installPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('pkg install -y ' . self::escapeShellArgument($package));
    }

    public function installPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg install -y ' . self::escapeShellArgument($package), $sudoPassword);
    }

    public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg install -y ' . self::escapeShellArgument($package));
    }

    public function uninstallPackage(string $package): RemoteCommandOutput
    {
        return $this->remoteExec('pkg delete -y ' . self::escapeShellArgument($package));
    }

    public function uninstallPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg delete -y ' . self::escapeShellArgument($package), $sudoPassword);
    }

    public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg delete -y ' . self::escapeShellArgument($package));
    }

    public function upgradePackages(): RemoteCommandOutput
    {
        return $this->remoteExec('pkg update && pkg upgrade -y');
    }

    public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg update && pkg upgrade -y', $sudoPassword);
    }

    public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput
    {
        return $this->executeWithSudo('pkg update && pkg upgrade -y');
    }
}
