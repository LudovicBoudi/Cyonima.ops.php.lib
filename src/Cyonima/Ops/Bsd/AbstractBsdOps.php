<?php

declare(strict_types=1);

namespace Cyonima\Ops\Bsd;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;
use Cyonima\Ops\Traits\UnixFileTrait;
use Cyonima\Ops\Traits\Bsd\BsdNetworkTrait;
use Cyonima\Ops\Traits\Bsd\BsdServiceTrait;
use Cyonima\Ops\Traits\Bsd\BsdUserTrait;

/**
 * Abstract base class for BSD operations
 *
 * Provides common FreeBSD/OpenBSD helpers for remote hosts via SSH.
 */
abstract class AbstractBsdOps extends AbstractOps
{
    use BsdServiceTrait;
    use BsdUserTrait;
    use BsdNetworkTrait;
    use UnixFileTrait;

    /**
     * Return a friendly BSD distribution name
     */
    abstract public function getDistribution(): string;

    /**
     * Retrieve BSD version information
     */
    public function getBsdVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('uname -sr');
    }

    /**
     * Execute a command with sudo
     *
     * Uses a temporary file with restricted permissions to pass the password
     * to sudo -S, avoiding exposure in the process list.
     */
    protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput
    {
        if ($sudoPassword !== null) {
            $encoded = base64_encode($sudoPassword);
            $tmpFile = '/tmp/._sudo_' . bin2hex(random_bytes(8));
            $tmp = self::escapeShellArgument($tmpFile);
            // Preserve the command's exit code; the trailing rm must not mask it.
            $script = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . $tmp
                . ' && chmod 400 ' . $tmp
                . ' && sudo -S sh -lc ' . self::escapeShellArgument($command) . ' < ' . $tmp
                . '; __rc=$?; rm -f ' . $tmp . '; exit $__rc';
            return $this->remoteExec($script);
        }

        return $this->remoteExec('sudo sh -lc ' . self::escapeShellArgument($command));
    }

    abstract public function installPackage(string $package): RemoteCommandOutput;
    abstract public function installPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput;
    abstract public function installPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput;
    abstract public function uninstallPackage(string $package): RemoteCommandOutput;
    abstract public function uninstallPackageWithPrivilege(string $package, string $sudoPassword): RemoteCommandOutput;
    abstract public function uninstallPackageWithPrivilegeNoPwd(string $package): RemoteCommandOutput;
    abstract public function upgradePackages(): RemoteCommandOutput;
    abstract public function upgradePackagesWithPrivilege(string $sudoPassword): RemoteCommandOutput;
    abstract public function upgradePackagesWithPrivilegeNoPwd(): RemoteCommandOutput;
}
