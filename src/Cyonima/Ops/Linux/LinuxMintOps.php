<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Linux Mint operations
 *
 * Linux Mint is based on Ubuntu; reuse `UbuntuOps` implementations.
 */
class LinuxMintOps extends UbuntuOps
{
    /**
     * Return a friendly distribution name
     */
    public function getDistribution(): string
    {
        return 'Linux Mint';
    }

    // Additional Mint-specific overrides can be added here.
}
