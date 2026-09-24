<?php

declare(strict_types=1);

namespace Cyonima\Ops\Virtualbox;

/**
 * Concrete VirtualBox operations helper class
 *
 * Provides a direct interface to manage virtual machines and related resources
 * via VBoxManage over SSH connections.
 *
 * Example usage:
 * ```php
 * $vbox = new VirtualboxOps();
 * $vbox->setHost('vbox-host.example.local')
 *      ->setCredentials('vboxuser', 'password')
 *      ->setSshPort(22);
 * $vbox->openConnection();
 * $vbox->listVMs();
 * $vbox->createVm('test-vm', 'Ubuntu_64', '2048', '2');
 * $vbox->startVm('test-vm', 'headless');
 * $vbox->closeConnection();
 * ```
 */
class VirtualboxOps extends AbstractVirtualboxOps
{
}
