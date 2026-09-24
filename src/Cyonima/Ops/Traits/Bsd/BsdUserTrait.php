<?php

declare(strict_types=1);

namespace Cyonima\Ops\Traits\Bsd;

use Cyonima\Ops\RemoteCommandOutput;

trait BsdUserTrait
{
    abstract public function remoteExec(string $command): RemoteCommandOutput;

    abstract protected static function escapeShellArgument(string $argument): string;

    public function addUser(string $username, string $password, ?string $group = null): RemoteCommandOutput
    {
        $escapedUser = self::escapeShellArgument($username);
        $escapedPassword = self::escapeShellArgument($username . ':' . $password);
        $script = 'if command -v pw >/dev/null 2>&1; then ' .
            'pw useradd -n ' . $escapedUser . ' -m -s /bin/sh && ' .
            'echo ' . $escapedPassword . ' | chpasswd ' .
            'elif command -v useradd >/dev/null 2>&1; then ' .
            'useradd -m -s /bin/sh ' . $escapedUser . ' && ' .
            'echo ' . $escapedPassword . ' | chpasswd ' .
            'else echo \'No supported user creation tool found.\' ; exit 1; fi';

        return $this->remoteExec($script);
    }

    public function deleteUser(string $username): RemoteCommandOutput
    {
        $escapedUser = self::escapeShellArgument($username);
        $script = 'if command -v pw >/dev/null 2>&1; then ' .
            'pw userdel -n ' . $escapedUser . ' -r ' .
            'elif command -v userdel >/dev/null 2>&1; then ' .
            'userdel -r ' . $escapedUser . ' ' .
            'else echo \'No supported user deletion tool found.\' ; exit 1; fi';

        return $this->remoteExec($script);
    }

    public function changePassword(string $username, string $newPassword): RemoteCommandOutput
    {
        $escapedUser = self::escapeShellArgument($username);
        $escapedPassword = self::escapeShellArgument($newPassword);
        $script = 'if command -v chpasswd >/dev/null 2>&1; then ' .
            'echo ' . self::escapeShellArgument($username . ':' . $newPassword) . ' | chpasswd ' .
            'elif command -v passwd >/dev/null 2>&1; then ' .
            'printf \'%s\\n%s\\n\' ' . $escapedPassword . ' ' . $escapedPassword . ' | passwd ' . $escapedUser . ' ' .
            'else echo \'No supported password management tool found.\' ; exit 1; fi';

        return $this->remoteExec($script);
    }
}
