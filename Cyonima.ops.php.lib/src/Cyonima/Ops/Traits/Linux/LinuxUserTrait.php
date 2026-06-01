<?php

declare(strict_types=1);

namespace Cyonima\Ops\Traits\Linux;

use Cyonima\Ops\RemoteCommandOutput;

trait LinuxUserTrait
{
    abstract public function remoteExec(string $command): RemoteCommandOutput;

    abstract protected static function escapeShellArgument(string $argument): string;

    abstract protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput;

    public function addUser(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        $cmd = 'useradd -m -s /bin/bash ' . self::escapeShellArgument($username);
        if ($group !== null) {
            $cmd .= ' -g ' . self::escapeShellArgument($group);
        }
        $cmd .= ' && echo ' . self::escapeShellArgument($username . ':' . $password) . ' | chpasswd';
        return $this->remoteExec($cmd);
    }

    public function addUserWithPrivilege(string $username, string $password, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'useradd -m -s /bin/bash ' . self::escapeShellArgument($username) . ' && echo ' . self::escapeShellArgument($username . ':' . $password) . ' | chpasswd';
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    public function deleteUser(string $username): RemoteCommandOutput
    {
        return $this->executeWithSudo('userdel -r ' . self::escapeShellArgument($username));
    }

    public function deleteUserWithPrivilege(string $username, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('userdel -r ' . self::escapeShellArgument($username), $sudoPassword);
    }

    public function changePassword(string $username, string $newPassword): RemoteCommandOutput
    {
        $cmd = 'echo ' . self::escapeShellArgument($username . ':' . $newPassword) . ' | chpasswd';
        return $this->remoteExec($cmd);
    }

    public function changePasswordWithPrivilege(string $username, string $newPassword, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'echo ' . self::escapeShellArgument($username . ':' . $newPassword) . ' | chpasswd';
        return $this->executeWithSudo($cmd, $sudoPassword);
    }
}
