<?php

declare(strict_types=1);

namespace Cyonima\Ops\Traits\Linux;

use Cyonima\Ops\RemoteCommandOutput;

trait LinuxSystemTrait
{
    abstract public function remoteExec(string $command): RemoteCommandOutput;

    abstract protected static function escapeShellArgument(string $argument): string;

    abstract protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput;

    public function listProcesses(string $filter = ''): RemoteCommandOutput
    {
        if ($filter === '') {
            return $this->remoteExec('ps -ef');
        }

        return $this->remoteExec('ps -ef | grep -E ' . self::escapeShellArgument($filter) . ' | grep -v grep || true');
    }

    public function killProcess(int $pid): RemoteCommandOutput
    {
        return $this->remoteExec('kill -15 ' . self::escapeShellArgument((string) $pid));
    }

    public function killProcessByName(string $name): RemoteCommandOutput
    {
        return $this->remoteExec('pkill -f ' . self::escapeShellArgument($name));
    }

    public function getTopProcesses(int $count = 10): RemoteCommandOutput
    {
        return $this->remoteExec('ps -eo pid,comm,%cpu,%mem --sort=-%cpu | head -n ' . self::escapeShellArgument((string) $count));
    }

    public function readLogFile(string $path, int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('tail -n ' . self::escapeShellArgument((string) $lines) . ' ' . self::escapeShellArgument($path));
    }

    public function readJournalLog(string $unit, int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('journalctl -u ' . self::escapeShellArgument($unit) . ' -n ' . self::escapeShellArgument((string) $lines) . ' --no-pager');
    }

    public function tailJournalLog(string $unit, int $lines = 100): RemoteCommandOutput
    {
        return $this->remoteExec('journalctl -u ' . self::escapeShellArgument($unit) . ' -n ' . self::escapeShellArgument((string) $lines) . ' --follow --no-pager');
    }

    public function getNetworkInterfaces(): RemoteCommandOutput
    {
        return $this->remoteExec('ip addr show || ifconfig -a');
    }

    public function getRoutingTable(): RemoteCommandOutput
    {
        return $this->remoteExec('ip route show || netstat -rn');
    }

    public function addNetworkRoute(string $destination, string $gateway, ?string $device = null): RemoteCommandOutput
    {
        $cmd = 'ip route add ' . self::escapeShellArgument($destination) . ' via ' . self::escapeShellArgument($gateway);
        if ($device !== null && $device !== '') {
            $cmd .= ' dev ' . self::escapeShellArgument($device);
        }
        return $this->remoteExec($cmd);
    }

    public function getFirewallStatus(): RemoteCommandOutput
    {
        $script = <<<'BASH'
if command -v ufw >/dev/null 2>&1; then
  ufw status verbose
elif command -v firewall-cmd >/dev/null 2>&1; then
  firewall-cmd --state
else
  echo 'No supported firewall manager found.'
  exit 1
fi
BASH;
        return $this->remoteExec($script);
    }

    public function enableFirewall(): RemoteCommandOutput
    {
        $script = <<<'BASH'
if command -v ufw >/dev/null 2>&1; then
  ufw --force enable
elif command -v firewall-cmd >/dev/null 2>&1; then
  firewall-cmd --set-default-zone=public
  firewall-cmd --reload
else
  echo 'No supported firewall manager found.'
  exit 1
fi
BASH;
        return $this->executeWithSudo($script);
    }

    public function disableFirewall(): RemoteCommandOutput
    {
        $script = <<<'BASH'
if command -v ufw >/dev/null 2>&1; then
  ufw disable
elif command -v firewall-cmd >/dev/null 2>&1; then
  firewall-cmd --panic-off
  firewall-cmd --reload
else
  echo 'No supported firewall manager found.'
  exit 1
fi
BASH;
        return $this->executeWithSudo($script);
    }

    public function addFirewallRule(string $port, string $protocol = 'tcp'): RemoteCommandOutput
    {
        $script = 'if command -v ufw >/dev/null 2>&1; then ' .
            'ufw allow ' . self::escapeShellArgument($port . '/' . $protocol) . '; ' .
            'elif command -v firewall-cmd >/dev/null 2>&1; then ' .
            'firewall-cmd --permanent --add-port=' . self::escapeShellArgument($port . '/' . $protocol) . ' && firewall-cmd --reload; ' .
            'else echo "No supported firewall manager found."; exit 1; fi';

        return $this->executeWithSudo($script);
    }

    public function getSelinuxStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('getenforce || true');
    }

    public function setSelinuxMode(string $mode): RemoteCommandOutput
    {
        return $this->executeWithSudo('setenforce ' . self::escapeShellArgument($mode));
    }

    public function getAppArmorStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('apparmor_status || true');
    }

    public function enforceAppArmorProfile(string $profile): RemoteCommandOutput
    {
        return $this->executeWithSudo('aa-enforce ' . self::escapeShellArgument($profile));
    }
}
