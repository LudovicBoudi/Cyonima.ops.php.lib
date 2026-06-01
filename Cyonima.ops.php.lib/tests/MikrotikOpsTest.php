<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Network\MikrotikOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class MikrotikOpsTest extends TestCase
{
    private function createOpsWithCapture(): object
    {
        return new class() extends MikrotikOps {
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

    public function testGetSystemIdentity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSystemIdentity();

        $this->assertSame('/system identity print', $ops->capturedCommand);
    }

    public function testSetSystemIdentity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSystemIdentity('MyRouter');

        $this->assertSame('/system identity set name=MyRouter', $ops->capturedCommand);
    }

    public function testSetSystemIdentityWithSpaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSystemIdentity('Main Office Router');

        $this->assertSame('/system identity set name="Main Office Router"', $ops->capturedCommand);
    }

    public function testGetVersion(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getVersion();

        $this->assertSame('/system resource print', $ops->capturedCommand);
    }

    public function testGetUptime(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getUptime();

        $this->assertSame('/system resource uptime print', $ops->capturedCommand);
    }

    public function testGetResources(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getResources();

        $this->assertSame('/system resource print', $ops->capturedCommand);
    }

    public function testGetHealth(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getHealth();

        $this->assertSame('/system health print', $ops->capturedCommand);
    }

    public function testReboot(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->reboot();

        $this->assertSame('/system reboot', $ops->capturedCommand);
    }

    public function testShutdown(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->shutdown();

        $this->assertSame('/system shutdown', $ops->capturedCommand);
    }

    public function testGetInterfaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaces();

        $this->assertSame('/interface print', $ops->capturedCommand);
    }

    public function testEnableInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableInterface('ether1');

        $this->assertSame('/interface enable ether1', $ops->capturedCommand);
    }

    public function testDisableInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableInterface('wlan1');

        $this->assertSame('/interface disable wlan1', $ops->capturedCommand);
    }

    public function testSetInterfaceComment(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceComment('ether2', 'Uplink to ISP');

        $this->assertSame('/interface set [find name=ether2] comment="Uplink to ISP"', $ops->capturedCommand);
    }

    public function testGetIpAddresses(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getIpAddresses();

        $this->assertSame('/ip address print', $ops->capturedCommand);
    }

    public function testAddIpAddress(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addIpAddress('10.0.0.1/24', 'ether1');

        $this->assertSame('/ip address add address=10.0.0.1/24 interface=ether1', $ops->capturedCommand);
    }

    public function testAddIpAddressWithNetwork(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addIpAddress('10.0.0.1/24', 'ether1', '10.0.0.0');

        $this->assertSame('/ip address add address=10.0.0.1/24 interface=ether1 network=10.0.0.0', $ops->capturedCommand);
    }

    public function testRemoveIpAddress(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeIpAddress('10.0.0.1/24', 'ether1');

        $this->assertSame('/ip address remove [find address=10.0.0.1/24 and interface=ether1]', $ops->capturedCommand);
    }

    public function testGetRoutes(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getRoutes();

        $this->assertSame('/ip route print', $ops->capturedCommand);
    }

    public function testAddRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addRoute('10.0.0.0/8', '192.168.1.1');

        $this->assertSame('/ip route add dst-address=10.0.0.0/8 gateway=192.168.1.1', $ops->capturedCommand);
    }

    public function testAddRouteWithDistance(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addRoute('0.0.0.0/0', '192.168.1.1', 5);

        $this->assertSame('/ip route add dst-address=0.0.0.0/0 gateway=192.168.1.1 distance=5', $ops->capturedCommand);
    }

    public function testRemoveRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeRoute('10.0.0.0/8');

        $this->assertSame('/ip route remove [find dst-address=10.0.0.0/8]', $ops->capturedCommand);
    }

    public function testSetDefaultGateway(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDefaultGateway('192.168.1.254');

        $this->assertSame('/ip route add dst-address=0.0.0.0/0 gateway=192.168.1.254', $ops->capturedCommand);
    }

    public function testGetArpTable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getArpTable();

        $this->assertSame('/ip arp print', $ops->capturedCommand);
    }

    public function testGetDhcpLeases(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getDhcpLeases();

        $this->assertSame('/ip dhcp-server lease print', $ops->capturedCommand);
    }

    public function testGetFirewallRules(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getFirewallRules();

        $this->assertSame('/ip firewall filter print', $ops->capturedCommand);
    }

    public function testAddFirewallRuleSimple(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addFirewallRule('input', 'drop');

        $this->assertSame('/ip firewall filter add chain=input action=drop', $ops->capturedCommand);
    }

    public function testAddFirewallRuleFull(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addFirewallRule(
            chain: 'forward',
            action: 'drop',
            protocol: 'tcp',
            srcAddress: '10.0.0.0/8',
            dstAddress: '192.168.1.0/24',
            dstPort: 80,
            inInterface: 'ether1',
            outInterface: 'ether2',
            comment: 'Block HTTP'
        );

        $this->assertSame(
            '/ip firewall filter add chain=forward action=drop protocol=tcp'
            . ' src-address=10.0.0.0/8 dst-address=192.168.1.0/24'
            . ' dst-port=80 in-interface=ether1 out-interface=ether2'
            . ' comment="Block HTTP"',
            $ops->capturedCommand
        );
    }

    public function testRemoveFirewallRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeFirewallRule(3);

        $this->assertSame('/ip firewall filter remove numbers=3', $ops->capturedCommand);
    }

    public function testEnableFirewallRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableFirewallRule(2);

        $this->assertSame('/ip firewall filter enable numbers=2', $ops->capturedCommand);
    }

    public function testDisableFirewallRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableFirewallRule(5);

        $this->assertSame('/ip firewall filter disable numbers=5', $ops->capturedCommand);
    }

    public function testAddNatRuleMasquerade(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addNatRule('srcnat', 'masquerade');

        $this->assertSame('/ip firewall nat add chain=srcnat action=masquerade', $ops->capturedCommand);
    }

    public function testAddNatRuleDstnat(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addNatRule('dstnat', 'dst-nat', toPorts: 8080);

        $this->assertSame('/ip firewall nat add chain=dstnat action=dst-nat to-ports=8080', $ops->capturedCommand);
    }

    public function testGetNatRules(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getNatRules();

        $this->assertSame('/ip firewall nat print', $ops->capturedCommand);
    }

    public function testGetVlans(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getVlans();

        $this->assertSame('/interface vlan print', $ops->capturedCommand);
    }

    public function testCreateVlan(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlan(100, 'vlan100', 'ether1');

        $this->assertSame('/interface vlan add vlan-id=100 name=vlan100 interface=ether1', $ops->capturedCommand);
    }

    public function testRemoveVlan(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeVlan('vlan100');

        $this->assertSame('/interface vlan remove [find name=vlan100]', $ops->capturedCommand);
    }

    public function testGetBridges(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getBridges();

        $this->assertSame('/interface bridge print', $ops->capturedCommand);
    }

    public function testCreateBridge(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createBridge('bridge1');

        $this->assertSame('/interface bridge add name=bridge1', $ops->capturedCommand);
    }

    public function testCreateBridgeWithComment(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createBridge('bridge1', 'Main LAN bridge');

        $this->assertSame('/interface bridge add name=bridge1 comment="Main LAN bridge"', $ops->capturedCommand);
    }

    public function testRemoveBridge(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeBridge('bridge1');

        $this->assertSame('/interface bridge remove [find name=bridge1]', $ops->capturedCommand);
    }

    public function testAddBridgePort(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addBridgePort('bridge1', 'ether2');

        $this->assertSame('/interface bridge port add bridge=bridge1 interface=ether2', $ops->capturedCommand);
    }

    public function testRemoveBridgePort(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeBridgePort('bridge1', 'ether2');

        $this->assertSame('/interface bridge port remove [find bridge=bridge1 and interface=ether2]', $ops->capturedCommand);
    }

    public function testGetBridgePorts(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getBridgePorts();

        $this->assertSame('/interface bridge port print', $ops->capturedCommand);
    }

    public function testGetWirelessRegistrations(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getWirelessRegistrations();

        $this->assertSame('/interface wireless registration-table print', $ops->capturedCommand);
    }

    public function testGetCapsmanRegistrations(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getCapsmanRegistrations();

        $this->assertSame('/caps-man registration-table print', $ops->capturedCommand);
    }

    public function testPing(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('8.8.8.8');

        $this->assertSame('/ping 8.8.8.8 count=5 size=64', $ops->capturedCommand);
    }

    public function testPingCustom(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('10.0.0.1', count: 10, size: 1500);

        $this->assertSame('/ping 10.0.0.1 count=10 size=1500', $ops->capturedCommand);
    }

    public function testTraceroute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->traceroute('google.com');

        $this->assertSame('/tool traceroute google.com', $ops->capturedCommand);
    }

    public function testGetUsers(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getUsers();

        $this->assertSame('/user print', $ops->capturedCommand);
    }

    public function testCreateUser(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createUser('admin', 'secret123');

        $this->assertSame('/user add name=admin password=secret123 group=full', $ops->capturedCommand);
    }

    public function testCreateUserWithGroup(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createUser('monitor', 'monpass', 'read');

        $this->assertSame('/user add name=monitor password=monpass group=read', $ops->capturedCommand);
    }

    public function testRemoveUser(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeUser('admin');

        $this->assertSame('/user remove [find name=admin]', $ops->capturedCommand);
    }

    public function testSetUserPassword(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setUserPassword('admin', 'newpass456');

        $this->assertSame('/user set [find name=admin] password=newpass456', $ops->capturedCommand);
    }

    public function testGetUserGroups(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getUserGroups();

        $this->assertSame('/user group print', $ops->capturedCommand);
    }

    public function testGetSnmpCommunities(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSnmpCommunities();

        $this->assertSame('/snmp community print', $ops->capturedCommand);
    }

    public function testAddSnmpCommunity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addSnmpCommunity('public');

        $this->assertSame('/snmp community add name=public address=0.0.0.0/0 security=read-only', $ops->capturedCommand);
    }

    public function testAddSnmpCommunityRestricted(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addSnmpCommunity('monitor', 'read-write', '10.0.0.0/8');

        $this->assertSame('/snmp community add name=monitor address=10.0.0.0/8 security=read-write', $ops->capturedCommand);
    }

    public function testRemoveSnmpCommunity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeSnmpCommunity('public');

        $this->assertSame('/snmp community remove [find name=public]', $ops->capturedCommand);
    }

    public function testEnableSnmp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableSnmp();

        $this->assertSame('/snmp set enabled=yes', $ops->capturedCommand);
    }

    public function testDisableSnmp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableSnmp();

        $this->assertSame('/snmp set enabled=no', $ops->capturedCommand);
    }

    public function testSetNtpServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setNtpServer('pool.ntp.org');

        $this->assertSame('/system ntp client set enabled=yes server-dns-names=pool.ntp.org', $ops->capturedCommand);
    }

    public function testDisableNtpClient(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableNtpClient();

        $this->assertSame('/system ntp client set enabled=no', $ops->capturedCommand);
    }

    public function testSetSyslogServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSyslogServer('192.168.1.100');

        $this->assertSame('/system logging action set 0 remote=192.168.1.100 remote-port=514', $ops->capturedCommand);
    }

    public function testExportConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exportConfig();

        $this->assertSame('/export', $ops->capturedCommand);
    }

    public function testExportConfigTerse(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exportConfigTerse();

        $this->assertSame('/export terse', $ops->capturedCommand);
    }

    public function testExportConfigCompact(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exportConfigCompact();

        $this->assertSame('/export compact', $ops->capturedCommand);
    }

    public function testSaveBackup(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->saveBackup('pre-upgrade-backup');

        $this->assertSame('/system backup save name=pre-upgrade-backup', $ops->capturedCommand);
    }

    public function testLoadBackup(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->loadBackup('pre-upgrade-backup');

        $this->assertSame('/system backup load name=pre-upgrade-backup', $ops->capturedCommand);
    }

    public function testGetFiles(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getFiles();

        $this->assertSame('/file print', $ops->capturedCommand);
    }

    public function testRemoveFile(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeFile('backup.backup');

        $this->assertSame('/file remove [find name=backup.backup]', $ops->capturedCommand);
    }

    public function testGetConnections(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getConnections();

        $this->assertSame('/ip firewall connection print', $ops->capturedCommand);
    }

    public function testGetDhcpClients(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getDhcpClients();

        $this->assertSame('/ip dhcp-client print', $ops->capturedCommand);
    }

    public function testGetNeighbors(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getNeighbors();

        $this->assertSame('/ip neighbor print', $ops->capturedCommand);
    }

    public function testGetScheduler(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getScheduler();

        $this->assertSame('/system scheduler print', $ops->capturedCommand);
    }

    public function testAddScheduler(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addScheduler('daily-reboot', '24:00:00', '/system reboot');

        $this->assertSame('/system scheduler add name=daily-reboot interval=24:00:00 on-event="/system reboot" start-time=startup', $ops->capturedCommand);
    }

    public function testRemoveScheduler(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeScheduler('daily-reboot');

        $this->assertSame('/system scheduler remove [find name=daily-reboot]', $ops->capturedCommand);
    }

    public function testExec(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exec('/ip route print detail');

        $this->assertSame('/ip route print detail', $ops->capturedCommand);
    }

    public function testGetClock(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getClock();

        $this->assertSame('/system clock print', $ops->capturedCommand);
    }

    public function testSetInterfaceMasterPort(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceMasterPort('ether3', 'bridge1');

        $this->assertSame('/interface set [find name=ether3] master-port=bridge1', $ops->capturedCommand);
    }

    public function testSetInterfaceMasterPortNone(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceMasterPort('ether3');

        $this->assertSame('/interface set [find name=ether3] master-port=none', $ops->capturedCommand);
    }

    public function testGetInterfaceDetail(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceDetail('ether1');

        $this->assertSame('/interface print detail where name=ether1', $ops->capturedCommand);
    }

    public function testSetDnsServers(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDnsServers(['8.8.8.8', '8.8.4.4']);

        $this->assertSame('/ip dns set servers=8.8.8.8,8.8.4.4 allow-remote-requests=yes', $ops->capturedCommand);
    }

    public function testGetDnsCache(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getDnsCache();

        $this->assertSame('/ip dns cache print', $ops->capturedCommand);
    }

    public function testGetDhcpServers(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getDhcpServers();

        $this->assertSame('/ip dhcp-server print', $ops->capturedCommand);
    }

    public function testAddLogRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addLogRule('192.168.1.100', 'remote', 'info');

        $this->assertSame('/system logging add action=remote topics=info remote=192.168.1.100', $ops->capturedCommand);
    }

    public function testGetLogByTopic(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLogByTopic('interface');

        $this->assertSame('/log print without-paging where topics~interface', $ops->capturedCommand);
    }

    public function testGetBandwidthServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getBandwidthServer();

        $this->assertSame('/tool bandwidth-server print', $ops->capturedCommand);
    }

    public function testEscapeMikrotikArgumentWithSpecialChars(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSystemIdentity('Router@DC-1 (main)');

        $this->assertSame('/system identity set name="Router@DC-1 (main)"', $ops->capturedCommand);
    }

    public function testEscapeMikrotikArgumentWithDoubleQuotes(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceComment('ether1', 'Interface "uplink" port');

        $this->assertSame('/interface set [find name=ether1] comment="Interface \\"uplink\\" port"', $ops->capturedCommand);
    }

    public function testEscapeMikrotikArgumentWithBackslash(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceComment('ether1', 'Path: C:\config');

        $this->assertSame('/interface set [find name=ether1] comment="Path: C:\\\\config"', $ops->capturedCommand);
    }

    public function testEscapeMikrotikArgumentEmpty(): void
    {
        $ref = new \ReflectionMethod(MikrotikOps::class, 'escapeMikrotikArgument');
        $ref->setAccessible(true);

        $result = $ref->invoke(null, '');
        $this->assertSame('""', $result);
    }

    public function testEscapeMikrotikArgumentSimpleValue(): void
    {
        $ref = new \ReflectionMethod(MikrotikOps::class, 'escapeMikrotikArgument');
        $ref->setAccessible(true);

        $result = $ref->invoke(null, 'ether1');
        $this->assertSame('ether1', $result);
    }

    public function testEscapeMikrotikArgumentIpWithPrefix(): void
    {
        $ref = new \ReflectionMethod(MikrotikOps::class, 'escapeMikrotikArgument');
        $ref->setAccessible(true);

        $result = $ref->invoke(null, '10.0.0.1/24');
        $this->assertSame('10.0.0.1/24', $result);
    }

    public function testEscapeMikrotikArgumentValueWithSpaces(): void
    {
        $ref = new \ReflectionMethod(MikrotikOps::class, 'escapeMikrotikArgument');
        $ref->setAccessible(true);

        $result = $ref->invoke(null, 'Main Office Router');
        $this->assertSame('"Main Office Router"', $result);
    }

    public function testEscapeMikrotikArgumentValueWithSpecialChars(): void
    {
        $ref = new \ReflectionMethod(MikrotikOps::class, 'escapeMikrotikArgument');
        $ref->setAccessible(true);

        $result = $ref->invoke(null, 'DC-1:Router@main');
        $this->assertSame('DC-1:Router@main', $result);
    }

    public function testMikrotikOpsReturnsRemoteCommandOutput(): void
    {
        $ops = $this->createOpsWithCapture();
        $result = $ops->getVersion();

        $this->assertInstanceOf(RemoteCommandOutput::class, $result);
    }

    public function testMultipleCallsAccumulateCommands(): void
    {
        $ops = $this->createOpsWithCapture();

        $ops->getSystemIdentity();
        $ops->getVersion();
        $ops->getInterfaces();

        $this->assertCount(3, $ops->capturedCommands);
        $this->assertSame('/system identity print', $ops->capturedCommands[0]);
        $this->assertSame('/system resource print', $ops->capturedCommands[1]);
        $this->assertSame('/interface print', $ops->capturedCommands[2]);
    }

    public function testCreateUserEscapesPassword(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createUser('admin', 'pass with spaces');

        $this->assertSame('/user add name=admin password="pass with spaces" group=full', $ops->capturedCommand);
    }

    public function testAddFirewallRuleEscapesComment(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addFirewallRule('input', 'drop', comment: 'Deny SSH from WAN');

        $this->assertSame('/ip firewall filter add chain=input action=drop comment="Deny SSH from WAN"', $ops->capturedCommand);
    }

    public function testSetDnsServersEscapesDomainNames(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDnsServers(['ns1.example.com', 'ns2.example.com']);

        $this->assertSame('/ip dns set servers=ns1.example.com,ns2.example.com allow-remote-requests=yes', $ops->capturedCommand);
    }
}
