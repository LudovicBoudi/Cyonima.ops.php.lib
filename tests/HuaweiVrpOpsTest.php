<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Network\HuaweiVrpOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class HuaweiVrpOpsTest extends TestCase
{
    private function createOpsWithCapture(): object
    {
        return new class() extends HuaweiVrpOps {
            public string $capturedCommand = '';
            public array $capturedCommands = [];

            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                $this->capturedCommands[] = $command;

                return new RemoteCommandOutput('', '', 0);
            }
        };
    }

    public function testEnterSystemView(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enterSystemView();

        $this->assertSame('system-view', $ops->capturedCommand);
    }

    public function testReturnToUserView(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->returnToUserView();

        $this->assertSame('return', $ops->capturedCommand);
    }

    public function testSaveConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->saveConfig();

        $this->assertSame('save', $ops->capturedCommand);
    }

    public function testDisablePagination(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disablePagination();

        $this->assertSame('screen-length 0 temporary', $ops->capturedCommand);
    }

    public function testGetHostname(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getHostname();

        $this->assertSame('display current-configuration | include sysname', $ops->capturedCommand);
    }

    public function testGetVersion(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getVersion();

        $this->assertSame('display version', $ops->capturedCommand);
    }

    public function testGetCurrentConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getCurrentConfig();

        $this->assertSame('display current-configuration', $ops->capturedCommand);
    }

    public function testGetSavedConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSavedConfig();

        $this->assertSame('display saved-configuration', $ops->capturedCommand);
    }

    public function testGetInterfaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaces();

        $this->assertSame('display interface brief', $ops->capturedCommand);
    }

    public function testGetInterfaceConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceConfig('GigabitEthernet0/0/1');

        $this->assertSame("display current-configuration interface 'GigabitEthernet0/0/1'", $ops->capturedCommand);
    }

    public function testGetInterfaceStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceStatus('GE0/0/1');

        $this->assertSame("display interface 'GE0/0/1'", $ops->capturedCommand);
    }

    public function testGetVlans(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getVlans();

        $this->assertSame('display vlan', $ops->capturedCommand);
    }

    public function testGetIpRouteTable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getIpRouteTable();

        $this->assertSame('display ip routing-table', $ops->capturedCommand);
    }

    public function testGetArpTable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getArpTable();

        $this->assertSame('display arp', $ops->capturedCommand);
    }

    public function testGetMacTable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getMacTable();

        $this->assertSame('display mac-address', $ops->capturedCommand);
    }

    public function testGetLldpNeighbors(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLldpNeighbors();

        $this->assertSame('display lldp neighbor brief', $ops->capturedCommand);
    }

    public function testGetUptime(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getUptime();

        $this->assertSame('display clock', $ops->capturedCommand);
    }

    public function testGetLogs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLogs();

        $this->assertSame('display logbuffer', $ops->capturedCommand);
    }

    public function testGetResources(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getResources();

        $this->assertSame('display cpu-usage && display memory-usage', $ops->capturedCommand);
    }

    public function testGetDeviceStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getDeviceStatus();

        $this->assertSame('display device', $ops->capturedCommand);
    }

    public function testPing(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('8.8.8.8');

        $this->assertSame("ping -c 5 '8.8.8.8'", $ops->capturedCommand);
    }

    public function testPingCustomCount(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('10.0.0.1', 10);

        $this->assertSame("ping -c 10 '10.0.0.1'", $ops->capturedCommand);
    }

    public function testTraceroute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->traceroute('google.com');

        $this->assertSame("tracert 'google.com'", $ops->capturedCommand);
    }

    public function testSetHostname(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setHostname('core-switch-1');

        $this->assertSame("system-view && sysname 'core-switch-1' && return && save", $ops->capturedCommand);
    }

    public function testSetHostnameWithSpaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setHostname('Core Switch 1');

        $this->assertSame("system-view && sysname 'Core Switch 1' && return && save", $ops->capturedCommand);
    }

    public function testSetBannerMotd(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setBannerMotd('Authorized access only');

        $this->assertSame("system-view && header login information 'Authorized access only' && return && save", $ops->capturedCommand);
    }

    public function testCreateUser(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createUser('netadmin', 'Str0ng!Pass');

        $this->assertSame(
            "system-view && aaa"
            . " && local-user 'netadmin' password cipher 'Str0ng!Pass'"
            . " && local-user 'netadmin' privilege level 15"
            . " && local-user 'netadmin' service-type ssh"
            . " && return && return && save",
            $ops->capturedCommand
        );
    }

    public function testCreateUserCustomPrivilege(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createUser('monitor', 'monpass', '1');

        $this->assertSame(
            "system-view && aaa"
            . " && local-user 'monitor' password cipher 'monpass'"
            . " && local-user 'monitor' privilege level 1"
            . " && local-user 'monitor' service-type ssh"
            . " && return && return && save",
            $ops->capturedCommand
        );
    }

    public function testDeleteUser(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteUser('netadmin');

        $this->assertSame("system-view && undo local-user 'netadmin' && return && save", $ops->capturedCommand);
    }

    public function testCreateVlan(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlan(100);

        $this->assertSame('system-view && vlan 100 && return && save', $ops->capturedCommand);
    }

    public function testCreateVlanWithName(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlan(100, 'servers');

        $this->assertSame("system-view && vlan 100 && name 'servers' && return && save", $ops->capturedCommand);
    }

    public function testCreateVlanBatch(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlanBatch([100, 200, 300]);

        $this->assertSame('system-view && vlan batch 100 200 300 && return && save', $ops->capturedCommand);
    }

    public function testDeleteVlan(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteVlan(100);

        $this->assertSame('system-view && undo vlan 100 && return && save', $ops->capturedCommand);
    }

    public function testSetInterfaceAccessVlan(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceAccessVlan('GE0/0/1', 50);

        $this->assertSame("system-view && interface 'GE0/0/1' && port link-type access && port default vlan 50 && return && save", $ops->capturedCommand);
    }

    public function testSetInterfaceTrunk(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceTrunk('GE0/0/2');

        $this->assertSame("system-view && interface 'GE0/0/2' && port link-type trunk && port trunk allow-pass vlan all && port trunk pvid vlan 1 && return && save", $ops->capturedCommand);
    }

    public function testSetInterfaceTrunkWithAllowedVlans(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceTrunk('GE0/0/2', '10 20 100-200', 10);

        $this->assertSame("system-view && interface 'GE0/0/2' && port link-type trunk && port trunk allow-pass vlan '10 20 100-200' && port trunk pvid vlan 10 && return && save", $ops->capturedCommand);
    }

    public function testSetInterfaceHybrid(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceHybrid('GE0/0/3', '10 20', '100 200');

        $this->assertSame(
            "system-view && interface 'GE0/0/3'"
            . " && port link-type hybrid"
            . " && port hybrid untagged vlan '10 20'"
            . " && port hybrid tagged vlan '100 200'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceDescription(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceDescription('GE0/0/1', 'Uplink to Core');

        $this->assertSame("system-view && interface 'GE0/0/1' && description 'Uplink to Core' && return && save", $ops->capturedCommand);
    }

    public function testShutdownInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->shutdownInterface('GE0/0/4');

        $this->assertSame("system-view && interface 'GE0/0/4' && shutdown && return && save", $ops->capturedCommand);
    }

    public function testUndoShutdownInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->undoShutdownInterface('GE0/0/4');

        $this->assertSame("system-view && interface 'GE0/0/4' && undo shutdown && return && save", $ops->capturedCommand);
    }

    public function testSetInterfaceIp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceIp('GE0/0/1', '10.0.0.1', '255.255.255.0');

        $this->assertSame(
            "system-view && interface 'GE0/0/1'"
            . " && ip address '10.0.0.1' '255.255.255.0'"
            . " && undo shutdown && return && save",
            $ops->capturedCommand
        );
    }

    public function testRemoveInterfaceIp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeInterfaceIp('GE0/0/1');

        $this->assertSame("system-view && interface 'GE0/0/1' && undo ip address && return && save", $ops->capturedCommand);
    }

    public function testCreateVlanif(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlanif(100, '192.168.1.254', '255.255.255.0');

        $this->assertSame(
            "system-view && interface vlanif 100"
            . " && ip address '192.168.1.254' '255.255.255.0'"
            . " && undo shutdown && return && save",
            $ops->capturedCommand
        );
    }

    public function testAddStaticRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute('10.0.0.0', '255.0.0.0', '192.168.1.1');

        $this->assertSame(
            "system-view"
            . " && ip route-static '10.0.0.0' '255.0.0.0' '192.168.1.1'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testAddStaticRouteWithPreference(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute('0.0.0.0', '0.0.0.0', '192.168.1.254', 10);

        $this->assertSame(
            "system-view"
            . " && ip route-static '0.0.0.0' '0.0.0.0' '192.168.1.254' preference 10"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testRemoveStaticRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeStaticRoute('10.0.0.0', '255.0.0.0', '192.168.1.1');

        $this->assertSame(
            "system-view"
            . " && undo ip route-static '10.0.0.0' '255.0.0.0' '192.168.1.1'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetDefaultGateway(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDefaultGateway('192.168.1.254');

        $this->assertSame(
            "system-view"
            . " && ip route-static 0.0.0.0 0.0.0.0 '192.168.1.254'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testEnableSsh(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableSsh('Str0ngP@ss');

        $this->assertSame(
            "system-view && user-interface vty 0 4"
            . " && authentication-mode aaa"
            . " && protocol inbound 'ssh'"
            . " && quit && aaa"
            . " && local-user admin password cipher 'Str0ngP@ss'"
            . " && local-user admin privilege level 15"
            . " && local-user admin service-type 'ssh'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetVtyConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setVtyConfig('aaa');

        $this->assertSame(
            "system-view && user-interface vty 0 4"
            . " && authentication-mode 'aaa'"
            . " && protocol inbound all"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testAddAclRuleBasic(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addAclRule(2000, 'deny', '10.0.0.0 0.255.255.255');

        $this->assertSame(
            "system-view && acl 2000"
            . " && rule 0 deny source '10.0.0.0 0.255.255.255'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testAddAclRuleAdvanced(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addAclRule(3000, 'permit', '10.0.0.0 0.255.255.255', '192.168.1.0', '0.0.0.255');

        $this->assertSame(
            "system-view && acl 3000"
            . " && rule 0 permit source '10.0.0.0 0.255.255.255'"
            . " destination '192.168.1.0' '0.0.0.255'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testDeleteAcl(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteAcl(2000);

        $this->assertSame('system-view && undo acl 2000 && return && save', $ops->capturedCommand);
    }

    public function testApplyAclInbound(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->applyAclInbound('GE0/0/1', 2000);

        $this->assertSame(
            "system-view && interface 'GE0/0/1'"
            . " && traffic-filter inbound acl 2000"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testApplyAclOutbound(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->applyAclOutbound('GE0/0/1', 3000);

        $this->assertSame(
            "system-view && interface 'GE0/0/1'"
            . " && traffic-filter outbound acl 3000"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetNtpServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setNtpServer('pool.ntp.org');

        $this->assertSame(
            "system-view"
            . " && ntp-service unicast-server 'pool.ntp.org'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetSnmpCommunity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSnmpCommunity('public');

        $this->assertSame(
            "system-view"
            . " && snmp-agent community read-only 'public'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetSnmpCommunityRw(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSnmpCommunity('private', 'rw');

        $this->assertSame(
            "system-view"
            . " && snmp-agent community read-write 'private'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetSnmpCommunityWithAcl(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSnmpCommunity('monitor', 'ro', 2000);

        $this->assertSame(
            "system-view"
            . " && snmp-agent community read-only 'monitor' acl 2000"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetSyslogServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSyslogServer('192.168.1.100');

        $this->assertSame(
            "system-view"
            . " && info-center loghost '192.168.1.100'"
            . " facility 'local0'"
            . " && info-center source default channel logbuffer log level 'informational'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testEnableLldp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableLldp();

        $this->assertSame('system-view && lldp enable && return && save', $ops->capturedCommand);
    }

    public function testDisableLldp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableLldp();

        $this->assertSame('system-view && undo lldp enable && return && save', $ops->capturedCommand);
    }

    public function testEnableInterfaceLldp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableInterfaceLldp('GE0/0/1');

        $this->assertSame(
            "system-view && interface 'GE0/0/1'"
            . " && lldp enable && return && save",
            $ops->capturedCommand
        );
    }

    public function testCreateEthTrunk(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createEthTrunk(1, ['GE0/0/1', 'GE0/0/2']);

        $this->assertSame(
            "system-view && interface Eth-Trunk 1"
            . " && mode lacp && quit"
            . " && interface 'GE0/0/1' && eth-trunk 1 && quit"
            . " && interface 'GE0/0/2' && eth-trunk 1 && quit"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceSpeedDuplex(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceSpeedDuplex('GE0/0/1', '1000', 'full');

        $this->assertSame(
            "system-view && interface 'GE0/0/1'"
            . " && speed '1000' && duplex 'full'"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceDhcpClient(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceDhcpClient('GE0/0/1');

        $this->assertSame(
            "system-view && interface 'GE0/0/1'"
            . " && ip address dhcp-alloc && undo shutdown"
            . " && return && save",
            $ops->capturedCommand
        );
    }

    public function testGetStpStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getStpStatus();

        $this->assertSame('display stp brief', $ops->capturedCommand);
    }

    public function testEnableStp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableStp();

        $this->assertSame('system-view && stp enable && return && save', $ops->capturedCommand);
    }

    public function testDisableStp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableStp();

        $this->assertSame('system-view && undo stp enable && return && save', $ops->capturedCommand);
    }

    public function testSetStpMode(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setStpMode('mstp');

        $this->assertSame("system-view && stp mode 'mstp' && return && save", $ops->capturedCommand);
    }

    public function testGetInterfaceVlanInfo(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceVlanInfo('GE0/0/1');

        $this->assertSame("display port vlan active 'GE0/0/1'", $ops->capturedCommand);
    }

    public function testGetLinkAggregation(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLinkAggregation();

        $this->assertSame('display eth-trunk', $ops->capturedCommand);
    }

    public function testGetOspfNeighbors(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getOspfNeighbors();

        $this->assertSame('display ospf peer brief', $ops->capturedCommand);
    }

    public function testExec(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exec('display ntp-service status');

        $this->assertSame('display ntp-service status', $ops->capturedCommand);
    }

    public function testHuaweiVrpOpsReturnsRemoteCommandOutput(): void
    {
        $ops = $this->createOpsWithCapture();
        $result = $ops->getVersion();

        $this->assertInstanceOf(RemoteCommandOutput::class, $result);
    }

    public function testMultipleCallsAccumulateCommands(): void
    {
        $ops = $this->createOpsWithCapture();

        $ops->getVersion();
        $ops->getInterfaces();
        $ops->getVlans();

        $this->assertCount(3, $ops->capturedCommands);
        $this->assertSame('display version', $ops->capturedCommands[0]);
        $this->assertSame('display interface brief', $ops->capturedCommands[1]);
        $this->assertSame('display vlan', $ops->capturedCommands[2]);
    }

    public function testCreateVlanWithNameEscapesName(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlan(10, 'DMZ VLAN');

        $this->assertSame("system-view && vlan 10 && name 'DMZ VLAN' && return && save", $ops->capturedCommand);
    }
}
