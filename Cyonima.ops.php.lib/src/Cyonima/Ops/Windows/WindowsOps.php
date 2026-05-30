<?php

declare(strict_types=1);

namespace Cyonima\Ops\Windows;

/**
 * Generic Windows operations class
 *
 * This concrete class exposes generic Windows management helpers by leveraging
 * PowerShell over SSH. It is intended for use with Windows hosts that support
 * remote command execution via OpenSSH or an equivalent SSH server.
 *
 * For native WinRM access, use WindowsWinRmOps with WinRmClient.
 */
class WindowsOps extends AbstractWindowsOps
{
    // Concrete class available for direct use.
}
