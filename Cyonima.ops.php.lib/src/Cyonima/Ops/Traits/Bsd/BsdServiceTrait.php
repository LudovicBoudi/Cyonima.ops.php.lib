<?php

declare(strict_types=1);

namespace Cyonima\Ops\Traits\Bsd;

use Cyonima\Ops\RemoteCommandOutput;

trait BsdServiceTrait
{
    abstract public function remoteExec(string $command): RemoteCommandOutput;

    abstract protected static function escapeShellArgument(string $argument): string;

    abstract protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput;

    public function getService(string $serviceName): RemoteCommandOutput
    {
        return $this->remoteExec('service ' . self::escapeShellArgument($serviceName) . ' status');
    }

    public function manageService(string $serviceName, string $action): RemoteCommandOutput
    {
        $action = strtolower($action);
        if (!in_array($action, ['start', 'stop', 'restart', 'status'], true)) {
            throw new \InvalidArgumentException('Unsupported service action: ' . $action);
        }

        return $this->remoteExec('service ' . self::escapeShellArgument($serviceName) . ' ' . self::escapeShellArgument($action));
    }

    public function manageServiceWithPrivilege(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }

    public function manageServiceWithPrivilegeNoPwd(string $serviceName, string $action): RemoteCommandOutput
    {
        return $this->manageService($serviceName, $action);
    }
}
