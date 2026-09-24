<?php

declare(strict_types=1);

namespace Cyonima\Ops\Backup;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Rsync operations for file synchronization and backup
 *
 * Provides remote file synchronization using rsync over SSH connections.
 * All operations execute on the connected remote host via remoteExec().
 * Supports remote-to-remote sync, local directory archiving, and
 * incremental backup workflows.
 *
 * Example usage:
 * ```php
 * $rsync = new RsyncOps();
 * $rsync->setHost('backup-server.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $rsync->openConnection();
 * $rsync->syncToRemote('/var/www', '/backup/www');
 * $rsync->incrementalBackup('/var/www', '/backup/www', '/backup/www.2024-01-01');
 * $rsync->closeConnection();
 * ```
 */
class RsyncOps extends AbstractOps
{
    public const string DEFAULT_RSYNC_OPTS = '-avz';

    /**
     * Sync a local directory (on the connected host) to a remote or local destination
     *
     * When $remoteHost is null, performs a local copy on the connected host.
     * When $remoteHost is set, syncs from connected host to the specified
     * remote host via SSH using the configured port and username.
     *
     * @param string $localPath  Source path on the connected host
     * @param string $remotePath Destination path (local or remote)
     * @param string|null $remoteHost Optional target host for remote-to-remote sync
     * @param array<string>|null $options Optional rsync options (overrides DEFAULT_RSYNC_OPTS)
     * @return RemoteCommandOutput
     */
    public function syncToRemote(
        string $localPath,
        string $remotePath,
        ?string $remoteHost = null,
        ?array $options = null
    ): RemoteCommandOutput {
        $opts = $options !== null ? implode(' ', $options) : self::DEFAULT_RSYNC_OPTS;
        $escapedLocal = self::escapeShellArgument($localPath);
        $escapedRemote = self::escapeShellArgument($remotePath);

        if ($remoteHost !== null) {
            $escapedHost = self::escapeShellArgument($remoteHost);
            $escapedUser = self::escapeShellArgument($this->username);
            $sshCmd = self::escapeShellArgument('ssh -p ' . $this->sshPort);
            $command = "rsync {$opts} -e {$sshCmd} {$escapedLocal} {$escapedUser}@{$escapedHost}:{$escapedRemote}";
        } else {
            $command = "rsync {$opts} {$escapedLocal} {$escapedRemote}";
        }

        return $this->remoteExec($command);
    }

    /**
     * Sync from a remote or local source to a directory on the connected host
     *
     * When $remoteHost is null, performs a local copy on the connected host.
     * When $remoteHost is set, syncs from the specified remote host to the
     * connected host via SSH.
     *
     * @param string $remotePath Source path (local or remote)
     * @param string $localPath  Destination path on the connected host
     * @param string|null $remoteHost Optional source host for remote-to-local sync
     * @param array<string>|null $options Optional rsync options (overrides DEFAULT_RSYNC_OPTS)
     * @return RemoteCommandOutput
     */
    public function syncFromRemote(
        string $remotePath,
        string $localPath,
        ?string $remoteHost = null,
        ?array $options = null
    ): RemoteCommandOutput {
        $opts = $options !== null ? implode(' ', $options) : self::DEFAULT_RSYNC_OPTS;
        $escapedLocal = self::escapeShellArgument($localPath);
        $escapedRemote = self::escapeShellArgument($remotePath);

        if ($remoteHost !== null) {
            $escapedHost = self::escapeShellArgument($remoteHost);
            $escapedUser = self::escapeShellArgument($this->username);
            $sshCmd = self::escapeShellArgument('ssh -p ' . $this->sshPort);
            $command = "rsync {$opts} -e {$sshCmd} {$escapedUser}@{$escapedHost}:{$escapedRemote} {$escapedLocal}";
        } else {
            $command = "rsync {$opts} {$escapedRemote} {$escapedLocal}";
        }

        return $this->remoteExec($command);
    }

    /**
     * Create a compressed tar archive of a directory on the connected host
     *
     * @param string $sourceDir   Directory to archive
     * @param string $archivePath Destination archive file path
     * @return RemoteCommandOutput
     */
    public function archiveDirectory(string $sourceDir, string $archivePath): RemoteCommandOutput
    {
        $command = 'tar -czf '
            . self::escapeShellArgument($archivePath) . ' '
            . self::escapeShellArgument($sourceDir);

        return $this->remoteExec($command);
    }

    /**
     * Extract a tar archive to a destination directory on the connected host
     *
     * @param string $archivePath Path to the archive file
     * @param string $destDir     Destination directory for extraction
     * @return RemoteCommandOutput
     */
    public function extractArchive(string $archivePath, string $destDir): RemoteCommandOutput
    {
        $command = 'tar -xzf '
            . self::escapeShellArgument($archivePath) . ' -C '
            . self::escapeShellArgument($destDir);

        return $this->remoteExec($command);
    }

    /**
     * Perform an incremental backup using rsync with --link-dest
     *
     * Uses rsync's --link-dest to create hard-linked snapshots.
     * When $linkDest is provided, only changed files are transferred;
     * unchanged files are hard-linked from the previous backup, saving
     * space and time.
     *
     * @param string $sourceDir Source directory to back up
     * @param string $backupDir Destination backup directory
     * @param string|null $linkDest Optional previous backup path for hard-link deduplication
     * @return RemoteCommandOutput
     */
    public function incrementalBackup(
        string $sourceDir,
        string $backupDir,
        ?string $linkDest = null
    ): RemoteCommandOutput {
        $command = 'rsync -avz';

        if ($linkDest !== null) {
            $command .= ' --link-dest=' . self::escapeShellArgument($linkDest);
        }

        $command .= ' ' . self::escapeShellArgument($sourceDir) . '/';
        $command .= ' ' . self::escapeShellArgument($backupDir) . '/';

        return $this->remoteExec($command);
    }

    /**
     * Restore files from a backup directory to a target directory
     *
     * Performs a simple rsync restore from the backup location to the
     * specified target location on the connected host.
     *
     * @param string $backupDir Source backup directory
     * @param string $targetDir Destination directory for restoration
     * @return RemoteCommandOutput
     */
    public function restoreFromBackup(string $backupDir, string $targetDir): RemoteCommandOutput
    {
        $command = 'rsync -avz '
            . self::escapeShellArgument($backupDir) . '/ '
            . self::escapeShellArgument($targetDir) . '/';

        return $this->remoteExec($command);
    }
}
