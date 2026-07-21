<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;
use Cyonima\Ops\Traits\UnixFileTrait;
use Cyonima\Ops\Traits\Linux\LinuxServiceTrait;
use Cyonima\Ops\Traits\Linux\LinuxSystemTrait;
use Cyonima\Ops\Traits\Linux\LinuxUserTrait;

/**
 * Abstract base class for Linux operations
 *
 * Provides common methods for managing Linux systems
 */
abstract class AbstractLinuxOps extends AbstractOps
{
    use LinuxServiceTrait;
    use LinuxUserTrait;
    use LinuxSystemTrait;
    use UnixFileTrait;

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

    /**
     * Execute a command under sudo, with optional password
     *
     * Uses a temporary file with restricted permissions to pass the password
     * to sudo -S, avoiding exposure in the process list.
     *
     * @param string $command Command to execute
     * @param string|null $sudoPassword Optional sudo password
     * @return RemoteCommandOutput
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
                . ' && sudo -S bash -lc ' . self::escapeShellArgument($command) . ' < ' . $tmp
                . '; __rc=$?; rm -f ' . $tmp . '; exit $__rc';
            return $this->remoteExec($script);
        }

        return $this->remoteExec('sudo bash -lc ' . self::escapeShellArgument($command));
    }
}
