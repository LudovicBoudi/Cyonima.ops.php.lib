<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Network\CiscoOps;
use Cyonima\Ops\RemoteCommandOutput;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CiscoOpsTest extends TestCase
{
    private function createOpsWithCapture(): object
    {
        return new class() extends CiscoOps {
            public string $capturedCommand = '';
            /** @var array<int, string> */
            public array $capturedCommands = [];

            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                $this->capturedCommands[] = $command;

                return new RemoteCommandOutput('', '', 0);
            }
        };
    }

    public function testShowCommandIsPassedThrough(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getRunningConfig();

        $this->assertSame('show running-config', $ops->capturedCommand);
    }

    public function testGetInterfaceStatusIsNotShellQuoted(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceStatus('GigabitEthernet0/1');

        // No single quotes: IOS is not a POSIX shell.
        $this->assertSame('show interfaces GigabitEthernet0/1', $ops->capturedCommand);
    }

    public function testPingUsesRepeatCount(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('8.8.8.8', 3);

        $this->assertSame('ping 8.8.8.8 repeat 3', $ops->capturedCommand);
    }

    public function testSetHostnameUsesNewlineSeparatedConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setHostname('core-sw1');

        $expected = "configure terminal\nhostname core-sw1\nend\nwrite memory";
        $this->assertSame($expected, $ops->capturedCommand);
    }

    public function testSetInterfaceAccessVlanBuildsConfigSequence(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceAccessVlan('GigabitEthernet0/2', 100);

        $expected = "configure terminal\n"
            . "interface GigabitEthernet0/2\n"
            . "switchport mode access\n"
            . "switchport access vlan 100\n"
            . "exit\n"
            . "end\n"
            . "write memory";
        $this->assertSame($expected, $ops->capturedCommand);
    }

    public function testCreateUserIsNotShellQuoted(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createUser('netadmin', 'S3cret', 15);

        $expected = "configure terminal\n"
            . "username netadmin privilege 15 secret S3cret\n"
            . "end\n"
            . "write memory";
        $this->assertSame($expected, $ops->capturedCommand);
    }

    public function testTrunkWithoutAllowedVlansOmitsAllowedLine(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceTrunk('GigabitEthernet0/1');

        $expected = "configure terminal\n"
            . "interface GigabitEthernet0/1\n"
            . "switchport mode trunk\n"
            . "switchport trunk native vlan 1\n"
            . "exit\n"
            . "end\n"
            . "write memory";
        $this->assertSame($expected, $ops->capturedCommand);
    }

    public function testTrunkWithAllowedVlansIncludesAllowedLine(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceTrunk('GigabitEthernet0/1', '10,20,30', '99');

        $this->assertStringContainsString('switchport trunk native vlan 99', $ops->capturedCommand);
        $this->assertStringContainsString('switchport trunk allowed vlan 10,20,30', $ops->capturedCommand);
    }

    public function testCreatePortChannelIteratesMembers(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createPortChannel(1, ['GigabitEthernet0/1', 'GigabitEthernet0/2'], 'active');

        $expected = "configure terminal\n"
            . "interface port-channel 1\n"
            . "exit\n"
            . "interface GigabitEthernet0/1\n"
            . "channel-group 1 mode active\n"
            . "exit\n"
            . "interface GigabitEthernet0/2\n"
            . "channel-group 1 mode active\n"
            . "exit\n"
            . "end\n"
            . "write memory";
        $this->assertSame($expected, $ops->capturedCommand);
    }

    public function testAddStaticRouteWithDistance(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute('10.0.0.0', '255.0.0.0', '192.168.1.1', 5);

        $expected = "configure terminal\n"
            . "ip route 10.0.0.0 255.0.0.0 192.168.1.1 5\n"
            . "end\n"
            . "write memory";
        $this->assertSame($expected, $ops->capturedCommand);
    }

    public function testArgumentWithNewlineIsRejected(): void
    {
        $ops = $this->createOpsWithCapture();

        $this->expectException(InvalidArgumentException::class);
        // Attempt to inject an extra command via a line break.
        $ops->setHostname("evil\nno username admin");
    }
}
