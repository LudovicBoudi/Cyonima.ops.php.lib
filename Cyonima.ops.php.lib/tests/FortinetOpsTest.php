<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Network\FortinetOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class FortinetOpsTest extends TestCase
{
    private function createOpsWithCapture(): object
    {
        return new class() extends FortinetOps {
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

    public function testGetSystemStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSystemStatus();

        $this->assertSame('get system status', $ops->capturedCommand);
    }

    public function testGetVersion(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getVersion();

        $this->assertSame('get system status | grep Version', $ops->capturedCommand);
    }

    public function testGetPerformance(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getPerformance();

        $this->assertSame('get system performance status', $ops->capturedCommand);
    }

    public function testGetHaStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getHaStatus();

        $this->assertSame('get system ha status', $ops->capturedCommand);
    }

    public function testGetLicenses(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLicenses();

        $this->assertSame('get system license', $ops->capturedCommand);
    }

    public function testSetHostname(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setHostname('fg-primary');

        $this->assertSame("config system global && set hostname 'fg-primary' && end", $ops->capturedCommand);
    }

    public function testSetDnsServers(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDnsServers('8.8.8.8', '8.8.4.4');

        $this->assertSame(
            "config system dns && set primary '8.8.8.8' && set secondary '8.8.4.4' && end",
            $ops->capturedCommand
        );
    }

    public function testSetDnsServersPrimaryOnly(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDnsServers('10.0.0.254');

        $this->assertSame(
            "config system dns && set primary '10.0.0.254' && end",
            $ops->capturedCommand
        );
    }

    public function testSetNtpServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setNtpServer('pool.ntp.org');

        $this->assertSame(
            "config system ntp && set ntpsync enable && set server 'pool.ntp.org' && end",
            $ops->capturedCommand
        );
    }

    public function testSetNtpServerWithSecondary(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setNtpServer('ntp1.example.com', 'ntp2.example.com');

        $this->assertSame(
            "config system ntp && set ntpsync enable"
            . " && set server 'ntp1.example.com'"
            . " && set server 'ntp2.example.com'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testGetNtpConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getNtpConfig();

        $this->assertSame('get system ntp', $ops->capturedCommand);
    }

    public function testReboot(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->reboot();

        $this->assertSame('execute reboot', $ops->capturedCommand);
    }

    public function testShutdown(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->shutdown();

        $this->assertSame('execute shutdown', $ops->capturedCommand);
    }

    public function testFactoryReset(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->factoryReset();

        $this->assertSame('execute factoryreset', $ops->capturedCommand);
    }

    public function testGetInterfaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaces();

        $this->assertSame('get system interface', $ops->capturedCommand);
    }

    public function testGetInterfaceDetail(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceDetail('port1');

        $this->assertSame("show system interface 'port1'", $ops->capturedCommand);
    }

    public function testSetInterfaceIp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceIp('port1', '10.0.0.1', '255.255.255.0');

        $this->assertSame(
            "config system interface && edit 'port1'"
            . " && set mode static"
            . " && set ip '10.0.0.1' '255.255.255.0'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceIpWithAllowAccess(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceIp('port1', '10.0.0.1', '255.255.255.0', 'ping https ssh');

        $this->assertSame(
            "config system interface && edit 'port1'"
            . " && set mode static"
            . " && set ip '10.0.0.1' '255.255.255.0'"
            . " && set allowaccess 'ping https ssh'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceDhcp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceDhcp('wan1');

        $this->assertSame(
            "config system interface && edit 'wan1'"
            . " && set mode dhcp"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceAllowAccess(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceAllowAccess('port1', 'ping https snmp');

        $this->assertSame(
            "config system interface && edit 'port1'"
            . " && set allowaccess 'ping https snmp'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceDescription(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceDescription('port1', 'Uplink to ISP');

        $this->assertSame(
            "config system interface && edit 'port1'"
            . " && set alias 'Uplink to ISP'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testEnableInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableInterface('port2');

        $this->assertSame(
            "config system interface && edit 'port2'"
            . " && set status up && end",
            $ops->capturedCommand
        );
    }

    public function testDisableInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableInterface('port2');

        $this->assertSame(
            "config system interface && edit 'port2'"
            . " && set status down && end",
            $ops->capturedCommand
        );
    }

    public function testCreateVlan(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlan(100, 'port1');

        $this->assertSame(
            "config system interface && edit 'port1_vlan100'"
            . " && set vlanid 100"
            . " && set interface 'port1'"
            . " && set type vlan && end",
            $ops->capturedCommand
        );
    }

    public function testGetRoutes(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getRoutes();

        $this->assertSame('get router info routing-table all', $ops->capturedCommand);
    }

    public function testGetRouteForPrefix(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getRouteForPrefix('10.0.0.0/8');

        $this->assertSame("get router info routing-table details '10.0.0.0/8'", $ops->capturedCommand);
    }

    public function testGetArpTable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getArpTable();

        $this->assertSame('get system arp', $ops->capturedCommand);
    }

    public function testAddStaticRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute(1, '10.0.0.0/8', '192.168.1.1', 'port1');

        $this->assertSame(
            "config router static && edit 1"
            . " && set dst '10.0.0.0' '255.0.0.0'"
            . " && set gateway '192.168.1.1'"
            . " && set device 'port1'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testAddStaticRouteWithZeroId(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute(0, '0.0.0.0/0', '192.168.1.254', 'wan1');

        $this->assertSame(
            "config router static && edit 0"
            . " && set dst '0.0.0.0' '0.0.0.0'"
            . " && set gateway '192.168.1.254'"
            . " && set device 'wan1'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testAddStaticRouteHostRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute(2, '10.0.0.100', '192.168.1.1', 'port2');

        $this->assertSame(
            "config router static && edit 2"
            . " && set dst '10.0.0.100' '255.255.255.255'"
            . " && set gateway '192.168.1.1'"
            . " && set device 'port2'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testRemoveStaticRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeStaticRoute(1);

        $this->assertSame(
            "config router static && delete 1 && end",
            $ops->capturedCommand
        );
    }

    public function testSetDefaultGateway(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDefaultGateway('192.168.1.1', 'wan1');

        $this->assertSame(
            "config router static && edit 0"
            . " && set dst 0.0.0.0 0.0.0.0"
            . " && set gateway '192.168.1.1'"
            . " && set device 'wan1'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testGetPolicies(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getPolicies();

        $this->assertSame('get firewall policy', $ops->capturedCommand);
    }

    public function testAddPolicyMinimal(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addPolicy(0, 'Internet Access');

        $this->assertSame(
            "config firewall policy && edit 0"
            . " && set name 'Internet Access'"
            . " && set action 'accept'"
            . " && set srcintf 'any'"
            . " && set dstintf 'any'"
            . " && set srcaddr 'all'"
            . " && set dstaddr 'all'"
            . " && set service 'ALL'"
            . " && set schedule 'always'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testAddPolicyFull(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addPolicy(
            policyId: 5,
            name: 'Allow SSH',
            action: 'accept',
            srcintf: 'wan',
            dstintf: 'lan',
            srcaddr: 'office-net',
            dstaddr: 'web-server',
            service: 'HTTPS',
            schedule: 'always',
            logtraffic: 'all',
            comments: 'Allow HTTPS from office'
        );

        $this->assertSame(
            "config firewall policy && edit 5"
            . " && set name 'Allow SSH'"
            . " && set action 'accept'"
            . " && set srcintf 'wan'"
            . " && set dstintf 'lan'"
            . " && set srcaddr 'office-net'"
            . " && set dstaddr 'web-server'"
            . " && set service 'HTTPS'"
            . " && set schedule 'always'"
            . " && set logtraffic 'all'"
            . " && set comments 'Allow HTTPS from office'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testDeletePolicy(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deletePolicy(3);

        $this->assertSame(
            "config firewall policy && delete 3 && end",
            $ops->capturedCommand
        );
    }

    public function testEnablePolicy(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enablePolicy(2);

        $this->assertSame(
            "config firewall policy && edit 2 && set status enable && end",
            $ops->capturedCommand
        );
    }

    public function testDisablePolicy(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disablePolicy(2);

        $this->assertSame(
            "config firewall policy && edit 2 && set status disable && end",
            $ops->capturedCommand
        );
    }

    public function testGetAddresses(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getAddresses();

        $this->assertSame('get firewall address', $ops->capturedCommand);
    }

    public function testCreateAddress(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createAddress('web-server', '10.0.0.10', '255.255.255.255');

        $this->assertSame(
            "config firewall address && edit 'web-server'"
            . " && set subnet '10.0.0.10' '255.255.255.255'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testCreateAddressWithComment(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createAddress('office-net', '192.168.1.0', '255.255.255.0', 'Office LAN');

        $this->assertSame(
            "config firewall address && edit 'office-net'"
            . " && set subnet '192.168.1.0' '255.255.255.0'"
            . " && set comment 'Office LAN'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testDeleteAddress(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteAddress('web-server');

        $this->assertSame(
            "config firewall address && delete 'web-server' && end",
            $ops->capturedCommand
        );
    }

    public function testGetAddressGroups(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getAddressGroups();

        $this->assertSame('get firewall addrgrp', $ops->capturedCommand);
    }

    public function testCreateAddressGroup(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createAddressGroup('web-servers', ['web-01', 'web-02', 'web-03']);

        $this->assertSame(
            "config firewall addrgrp && edit 'web-servers'"
            . " && set member 'web-01' 'web-02' 'web-03'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testDeleteAddressGroup(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteAddressGroup('web-servers');

        $this->assertSame(
            "config firewall addrgrp && delete 'web-servers' && end",
            $ops->capturedCommand
        );
    }

    public function testGetServices(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getServices();

        $this->assertSame('get firewall service custom', $ops->capturedCommand);
    }

    public function testCreateService(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createService('tcp-8080', 'TCP', '8080');

        $this->assertSame(
            "config firewall service custom && edit 'tcp-8080'"
            . " && set protocol 'TCP'"
            . " && set tcp-portrange '8080'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testCreateServiceUdpRange(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createService('udp-sip', 'UDP', '5060-5090');

        $this->assertSame(
            "config firewall service custom && edit 'udp-sip'"
            . " && set protocol 'UDP'"
            . " && set udp-portrange '5060-5090'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testDeleteService(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteService('tcp-8080');

        $this->assertSame(
            "config firewall service custom && delete 'tcp-8080' && end",
            $ops->capturedCommand
        );
    }

    public function testGetAdmins(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getAdmins();

        $this->assertSame('get system admin', $ops->capturedCommand);
    }

    public function testCreateAdmin(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createAdmin('netadmin', 'Str0ng!Pass');

        $this->assertSame(
            "config system admin && edit 'netadmin'"
            . " && set password 'Str0ng!Pass'"
            . " && set accprofile 'super_admin'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testCreateAdminWithProfile(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createAdmin('monitor', 'monpass', 'read_only');

        $this->assertSame(
            "config system admin && edit 'monitor'"
            . " && set password 'monpass'"
            . " && set accprofile 'read_only'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testDeleteAdmin(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteAdmin('netadmin');

        $this->assertSame(
            "config system admin && delete 'netadmin' && end",
            $ops->capturedCommand
        );
    }

    public function testGetSnmpCommunities(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSnmpCommunities();

        $this->assertSame('get system snmp community', $ops->capturedCommand);
    }

    public function testAddSnmpCommunity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addSnmpCommunity(1, 'public');

        $this->assertSame(
            "config system snmp community && edit 1"
            . " && set name 'public'"
            . " && set query-v1 disable"
            . " && set query-v2c enable"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testDeleteSnmpCommunity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteSnmpCommunity(1);

        $this->assertSame(
            "config system snmp community && delete 1 && end",
            $ops->capturedCommand
        );
    }

    public function testGetSyslogConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSyslogConfig();

        $this->assertSame('get system log syslogd', $ops->capturedCommand);
    }

    public function testSetSyslogServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSyslogServer('192.168.1.100');

        $this->assertSame(
            "config system log syslogd"
            . " && set status enable"
            . " && set server '192.168.1.100'"
            . " && set facility 'local0'"
            . " && end",
            $ops->capturedCommand
        );
    }

    public function testBackupConfigTftp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->backupConfigTftp('192.168.1.200', 'fg-backup.conf');

        $this->assertSame(
            "execute backup config tftp '192.168.1.200' 'fg-backup.conf'",
            $ops->capturedCommand
        );
    }

    public function testRestoreConfigTftp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->restoreConfigTftp('192.168.1.200', 'fg-backup.conf');

        $this->assertSame(
            "execute restore config tftp '192.168.1.200' 'fg-backup.conf'",
            $ops->capturedCommand
        );
    }

    public function testGetHardwareInfo(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getHardwareInfo();

        $this->assertSame('get hardware status', $ops->capturedCommand);
    }

    public function testGetDiagnostics(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getDiagnostics();

        $this->assertSame('diagnose sys device', $ops->capturedCommand);
    }

    public function testPing(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('8.8.8.8');

        $this->assertSame("execute ping '8.8.8.8' 5", $ops->capturedCommand);
    }

    public function testPingCustomCount(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('10.0.0.1', 10);

        $this->assertSame("execute ping '10.0.0.1' 10", $ops->capturedCommand);
    }

    public function testTraceroute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->traceroute('google.com');

        $this->assertSame("execute traceroute 'google.com'", $ops->capturedCommand);
    }

    public function testGetSessions(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSessions();

        $this->assertSame('get system session status', $ops->capturedCommand);
    }

    public function testGetEventLogs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getEventLogs(100);

        $this->assertSame('execute log display event 100', $ops->capturedCommand);
    }

    public function testGetAttackLogs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getAttackLogs(200);

        $this->assertSame('execute log display attack 200', $ops->capturedCommand);
    }

    public function testExec(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exec('get system ha status');

        $this->assertSame('get system ha status', $ops->capturedCommand);
    }

    public function testParsePrefixNoSlash(): void
    {
        $ops = $this->createOpsWithCapture();
        $result = $ops->addStaticRoute(1, '10.0.0.100', '192.168.1.1', 'port2');

        $this->assertStringContainsString("set dst '10.0.0.100' '255.255.255.255'", $ops->capturedCommand);
    }

    public function testParsePrefixSlashZero(): void
    {
        $ops = $this->createOpsWithCapture();
        $result = $ops->addStaticRoute(1, '0.0.0.0/0', '192.168.1.1', 'wan1');

        $this->assertStringContainsString("set dst '0.0.0.0' '0.0.0.0'", $ops->capturedCommand);
    }

    public function testFortinetOpsReturnsRemoteCommandOutput(): void
    {
        $ops = $this->createOpsWithCapture();
        $result = $ops->getSystemStatus();

        $this->assertInstanceOf(RemoteCommandOutput::class, $result);
    }

    public function testMultipleCallsAccumulateCommands(): void
    {
        $ops = $this->createOpsWithCapture();

        $ops->getSystemStatus();
        $ops->getInterfaces();
        $ops->getRoutes();

        $this->assertCount(3, $ops->capturedCommands);
        $this->assertSame('get system status', $ops->capturedCommands[0]);
        $this->assertSame('get system interface', $ops->capturedCommands[1]);
        $this->assertSame('get router info routing-table all', $ops->capturedCommands[2]);
    }

    public function testCreateVlanCustomInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createVlan(200, 'port2', 200);

        $this->assertSame(
            "config system interface && edit 'port2_vlan200'"
            . " && set vlanid 200 && set interface 'port2'"
            . " && set type vlan && end",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceDescriptionEscapesSpaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceDescription('port3', 'DMZ network interface');

        $this->assertSame(
            "config system interface && edit 'port3'"
            . " && set alias 'DMZ network interface'"
            . " && end",
            $ops->capturedCommand
        );
    }
}
