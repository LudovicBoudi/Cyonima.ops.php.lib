<?php

declare(strict_types=1);

namespace Cyonima\Ops\Kvm;

/**
 * Concrete KVM/libvirt operations helper class
 *
 * Provides a direct interface to manage virtual machines and related resources
 * via virsh over SSH connections.
 *
 * Example usage:
 * ```php
 * $kvm = new KvmOps();
 * $kvm->setHost('kvm-host.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $kvm->openConnection();
 * $kvm->listVMs();
 * $kvm->createVm('test-vm', '2', '2048', '/var/lib/libvirt/images/test-vm.qcow2');
 * $kvm->startVm('test-vm');
 * $kvm->closeConnection();
 * ```
 */
class KvmOps extends AbstractKvmOps
{
}
