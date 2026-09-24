<?php

declare(strict_types=1);

namespace Cyonima\Ops\Linux;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * Zorin OS operations
 *
 * Zorin OS is an Ubuntu derivative; reuse `UbuntuOps` implementations.
 */
class ZorinOps extends UbuntuOps
{
    /**
     * Return a friendly distribution name
     */
    public function getDistribution(): string
    {
        return 'Zorin OS';
    }

    // Zorin-specific overrides may be added later.
}
