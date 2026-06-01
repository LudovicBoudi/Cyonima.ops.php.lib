<?php

declare(strict_types=1);

namespace Cyonima\Ops\Traits\Linux;

use Cyonima\Ops\RemoteCommandOutput;

trait LinuxServiceTrait
{
    abstract public function remoteExec(string $command): RemoteCommandOutput;

    abstract protected static function escapeShellArgument(string $argument): string;

    abstract protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput;

    public function systemctl(string $service, string $action): RemoteCommandOutput
    {
        return $this->remoteExec('systemctl ' . self::escapeShellArgument($action) . ' ' . self::escapeShellArgument($service));
    }

    public function systemctlWithPrivilege(string $service, string $action, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('systemctl ' . self::escapeShellArgument($action) . ' ' . self::escapeShellArgument($service), $sudoPassword);
    }

    public function systemctlWithPrivilegeNoPwd(string $service, string $action): RemoteCommandOutput
    {
        return $this->executeWithSudo('systemctl ' . self::escapeShellArgument($action) . ' ' . self::escapeShellArgument($service));
    }
}
