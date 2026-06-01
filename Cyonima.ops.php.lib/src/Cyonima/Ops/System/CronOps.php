<?php

declare(strict_types=1);

namespace Cyonima\Ops\System;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Crontab management module
 *
 * Provides methods to list, add, remove, replace, backup, and restore
 * cron jobs on remote systems via SSH.
 */
class CronOps extends AbstractOps
{
    /**
     * List cron jobs for a user
     *
     * @param string|null $user Optional username to list crontab for
     * @return RemoteCommandOutput
     */
    public function listCronJobs(?string $user = null): RemoteCommandOutput
    {
        $command = 'crontab -l';
        if ($user !== null) {
            $command .= ' -u ' . self::escapeShellArgument($user);
        }
        return $this->remoteExec($command);
    }

    /**
     * Add a new cron job
     *
     * @param string $schedule Cron schedule expression (e.g. "0 * * * *")
     * @param string $command The command to execute
     * @param string|null $user Optional username to add the job for
     * @return RemoteCommandOutput
     */
    public function addCronJob(string $schedule, string $command, ?string $user = null): RemoteCommandOutput
    {
        $userFlagRead = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';
        $userFlagWrite = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';
        $scheduleAndCommand = self::escapeShellArgument($schedule . ' ' . $command);

        return $this->remoteExec(
            '(crontab' . $userFlagRead . ' -l 2>/dev/null; echo ' . $scheduleAndCommand . ') | crontab' . $userFlagWrite . ' -'
        );
    }

    /**
     * Remove cron jobs matching a pattern
     *
     * @param string $pattern Grep pattern to match against cron jobs
     * @param string|null $user Optional username to remove jobs for
     * @return RemoteCommandOutput
     */
    public function removeCronJobsByPattern(string $pattern, ?string $user = null): RemoteCommandOutput
    {
        $userFlagRead = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';
        $userFlagWrite = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';

        return $this->remoteExec(
            '(crontab' . $userFlagRead . ' -l 2>/dev/null | grep -v ' . self::escapeShellArgument($pattern) . ') | crontab' . $userFlagWrite . ' -'
        );
    }

    /**
     * Replace all cron jobs with new content
     *
     * The content is base64-encoded to safely handle special characters.
     *
     * @param string $content Full crontab content to write
     * @param string|null $user Optional username to replace jobs for
     * @return RemoteCommandOutput
     */
    public function replaceAllCronJobs(string $content, ?string $user = null): RemoteCommandOutput
    {
        $encoded = base64_encode($content);
        $userFlag = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';

        return $this->remoteExec(
            'echo ' . $encoded . ' | base64 --decode | crontab' . $userFlag . ' -'
        );
    }

    /**
     * Backup cron jobs to a file
     *
     * @param string $backupPath Remote path to write the backup to
     * @param string|null $user Optional username to backup jobs for
     * @return RemoteCommandOutput
     */
    public function backupCronJobs(string $backupPath, ?string $user = null): RemoteCommandOutput
    {
        $userFlag = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';

        return $this->remoteExec(
            'crontab' . $userFlag . ' -l > ' . self::escapeShellArgument($backupPath)
        );
    }

    /**
     * Restore cron jobs from a backup file
     *
     * @param string $backupPath Remote path to the backup file
     * @param string|null $user Optional username to restore jobs for
     * @return RemoteCommandOutput
     */
    public function restoreCronJobs(string $backupPath, ?string $user = null): RemoteCommandOutput
    {
        $userFlag = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';

        return $this->remoteExec(
            'crontab' . $userFlag . ' ' . self::escapeShellArgument($backupPath)
        );
    }

    /**
     * Add a script as a cron job
     *
     * @param string $scriptPath Remote path to the script
     * @param string $schedule Cron schedule expression (e.g. "0 * * * *")
     * @param string|null $user Optional username to add the job for
     * @return RemoteCommandOutput
     */
    public function addScriptJob(string $scriptPath, string $schedule, ?string $user = null): RemoteCommandOutput
    {
        $userFlagRead = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';
        $userFlagWrite = $user !== null ? ' -u ' . self::escapeShellArgument($user) : '';

        return $this->remoteExec(
            '(crontab' . $userFlagRead . ' -l 2>/dev/null; echo ' . self::escapeShellArgument($schedule . ' ' . $scriptPath) . ') | crontab' . $userFlagWrite . ' -'
        );
    }
}
