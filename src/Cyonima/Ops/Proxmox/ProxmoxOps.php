<?php

declare(strict_types=1);

namespace Cyonima\Ops\Proxmox;

/**
 * Concrete Proxmox operations helper class
 *
 * Provides a direct interface to manage virtual machines, containers, storage,
 * and cluster resources via qm and pvesh over SSH connections.
 *
 * Example usage:
 * ```php
 * $proxmox = new ProxmoxOps();
 * $proxmox->setHost('proxmox-node.example.local')
 *         ->setCredentials('root', 'password')
 *         ->setSshPort(22);
 * $proxmox->openConnection();
 * $proxmox->listVMs();
 * $proxmox->createVm('100', 'test-vm', '2048', '2', 'local:0');
 * $proxmox->startVm('100');
 * $proxmox->getVmStatus('100');
 * $proxmox->closeConnection();
 * ```
 */
class ProxmoxOps extends AbstractProxmoxOps
{
}
