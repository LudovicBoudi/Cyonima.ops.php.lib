<?php

declare(strict_types=1);

namespace Cyonima\Ops\Traits;

use Cyonima\Ops\RemoteCommandOutput;

trait UnixFileTrait
{
    abstract public function remoteExec(string $command): RemoteCommandOutput;

    abstract protected static function escapeShellArgument(string $argument): string;

    abstract protected function executeWithSudo(string $command, ?string $sudoPassword = null): RemoteCommandOutput;

    public function readFile(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('cat ' . self::escapeShellArgument($path));
    }

    public function writeFile(string $path, string $content): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . self::escapeShellArgument($path);
        return $this->remoteExec($cmd);
    }

    public function writeFileWithPrivilege(string $path, string $content, string $sudoPassword): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $cmd = 'echo ' . self::escapeShellArgument($encoded) . ' | base64 --decode > ' . self::escapeShellArgument($path);
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    public function copyFile(string $source, string $destination): RemoteCommandOutput
    {
        return $this->remoteExec('cp -p ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination));
    }

    public function copyFileWithPrivilege(string $source, string $destination, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'cp -p ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination);
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    public function moveFile(string $source, string $destination): RemoteCommandOutput
    {
        return $this->remoteExec('mv ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination));
    }

    public function moveFileWithPrivilege(string $source, string $destination, string $sudoPassword): RemoteCommandOutput
    {
        $cmd = 'mv ' . self::escapeShellArgument($source) . ' ' . self::escapeShellArgument($destination);
        return $this->executeWithSudo($cmd, $sudoPassword);
    }

    public function removeFile(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('rm -rf ' . self::escapeShellArgument($path));
    }

    public function removeFileWithPrivilege(string $path, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('rm -rf ' . self::escapeShellArgument($path), $sudoPassword);
    }

    public function makeDirectory(string $path, string $mode = '0755'): RemoteCommandOutput
    {
        return $this->remoteExec('mkdir -p -m ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path));
    }

    public function makeDirectoryWithPrivilege(string $path, string $mode, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('mkdir -p -m ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path), $sudoPassword);
    }

    public function changePermissions(string $path, string $mode): RemoteCommandOutput
    {
        return $this->remoteExec('chmod ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path));
    }

    public function changePermissionsWithPrivilege(string $path, string $mode, string $sudoPassword): RemoteCommandOutput
    {
        return $this->executeWithSudo('chmod ' . self::escapeShellArgument($mode) . ' ' . self::escapeShellArgument($path), $sudoPassword);
    }

    public function changeOwner(string $path, string $owner, ?string $group = null): RemoteCommandOutput
    {
        $target = self::escapeShellArgument($owner . ($group !== null ? ':' . $group : ''));
        return $this->remoteExec('chown ' . $target . ' ' . self::escapeShellArgument($path));
    }

    public function changeOwnerWithPrivilege(string $path, string $owner, ?string $group, string $sudoPassword): RemoteCommandOutput
    {
        $target = self::escapeShellArgument($owner . ($group !== null ? ':' . $group : ''));
        return $this->executeWithSudo('chown ' . $target . ' ' . self::escapeShellArgument($path), $sudoPassword);
    }
}
