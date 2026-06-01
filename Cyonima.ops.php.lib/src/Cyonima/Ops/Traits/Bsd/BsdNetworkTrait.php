<?php

declare(strict_types=1);

namespace Cyonima\Ops\Traits\Bsd;

use Cyonima\Ops\RemoteCommandOutput;

trait BsdNetworkTrait
{
    abstract public function remoteExec(string $command): RemoteCommandOutput;

    abstract protected static function escapeShellArgument(string $argument): string;

    abstract protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput;

    public function getNetworkInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('ifconfig -a');
    }

    public function getRoutingTable(): RemoteCommandOutput
    {
        return $this->remoteExec('netstat -rn');
    }

    public function addNetworkRoute(string $destination, string $gateway, ?string $interface = null): RemoteCommandOutput
    {
        $cmd = 'route add -net ' . self::escapeShellArgument($destination) . ' ' . self::escapeShellArgument($gateway);
        if ($interface !== null && $interface !== '') {
            $cmd .= ' -iface ' . self::escapeShellArgument($interface);
        }
        return $this->executeWithSudo($cmd);
    }

    public function getFirewallStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('pfctl -s info || true');
    }

    public function enableFirewall(): RemoteCommandOutput
    {
        return $this->executeWithSudo('pfctl -e');
    }

    public function disableFirewall(): RemoteCommandOutput
    {
        return $this->executeWithSudo('pfctl -d');
    }

    public function addFirewallRule(string $rule): RemoteCommandOutput
    {
        $cmd = 'echo ' . self::escapeShellArgument($rule) . ' | pfctl -f -';
        return $this->executeWithSudo($cmd);
    }
}
