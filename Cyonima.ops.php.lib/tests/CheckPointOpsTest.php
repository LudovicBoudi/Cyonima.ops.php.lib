<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Network\CheckPointOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class CheckPointOpsTest extends TestCase
{
    private function createOpsWithCapture(): object
    {
        return new class() extends CheckPointOps {
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

    public function testGetVersion(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getVersion();

        $this->assertSame('show version all', $ops->capturedCommand);
    }

    public function testGetAssetInfo(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getAssetInfo();

        $this->assertSame('show asset all', $ops->capturedCommand);
    }

    public function testGetHostname(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getHostname();

        $this->assertSame('show configuration hostname', $ops->capturedCommand);
    }

    public function testSetHostname(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setHostname('cp-gw-primary');

        $this->assertSame("set hostname 'cp-gw-primary' && save config", $ops->capturedCommand);
    }

    public function testGetTime(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getTime();

        $this->assertSame('show time', $ops->capturedCommand);
    }

    public function testGetNtpStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getNtpStatus();

        $this->assertSame('show ntp', $ops->capturedCommand);
    }

    public function testSetNtpServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setNtpServer('pool.ntp.org');

        $this->assertSame(
            "set ntp server 'pool.ntp.org' && set ntp active on && save config",
            $ops->capturedCommand
        );
    }

    public function testGetDnsConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getDnsConfig();

        $this->assertSame('show dns', $ops->capturedCommand);
    }

    public function testSetDnsServersBoth(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDnsServers('8.8.8.8', '8.8.4.4');

        $this->assertSame(
            "set dns primary '8.8.8.8' && set dns secondary '8.8.4.4' && save config",
            $ops->capturedCommand
        );
    }

    public function testSetDnsServersPrimaryOnly(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDnsServers('10.0.0.254');

        $this->assertSame(
            "set dns primary '10.0.0.254' && save config",
            $ops->capturedCommand
        );
    }

    public function testReboot(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->reboot();

        $this->assertSame('reboot', $ops->capturedCommand);
    }

    public function testShutdown(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->shutdown();

        $this->assertSame('shutdown', $ops->capturedCommand);
    }

    public function testSaveConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->saveConfig();

        $this->assertSame('save config', $ops->capturedCommand);
    }

    public function testGetInterfaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaces();

        $this->assertSame('show interface all', $ops->capturedCommand);
    }

    public function testGetInterfaceDetail(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceDetail('eth0');

        $this->assertSame("show interface 'eth0'", $ops->capturedCommand);
    }

    public function testSetInterfaceIp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceIp('eth0', '10.0.0.1', 24);

        $this->assertSame(
            "set interface 'eth0' ipv4-address '10.0.0.1' mask-length 24 && save config",
            $ops->capturedCommand
        );
    }

    public function testEnableInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableInterface('eth0');

        $this->assertSame("set interface 'eth0' state on && save config", $ops->capturedCommand);
    }

    public function testDisableInterface(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableInterface('eth0');

        $this->assertSame("set interface 'eth0' state off && save config", $ops->capturedCommand);
    }

    public function testSetInterfaceComment(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceComment('eth1', 'DMZ network');

        $this->assertSame(
            "set interface 'eth1' comments 'DMZ network' && save config",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceMtu(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceMtu('eth0', 9000);

        $this->assertSame("set interface 'eth0' mtu 9000 && save config", $ops->capturedCommand);
    }

    public function testGetRoutes(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getRoutes();

        $this->assertSame('show route', $ops->capturedCommand);
    }

    public function testGetArpTable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getArpTable();

        $this->assertSame('show arp all', $ops->capturedCommand);
    }

    public function testAddStaticRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute('10.0.0.0/8', '192.168.1.1');

        $this->assertSame(
            "set static-route '10.0.0.0/8' nexthop gateway address '192.168.1.1' on && save config",
            $ops->capturedCommand
        );
    }

    public function testAddStaticRouteWithPriority(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute('0.0.0.0/0', '192.168.1.254', 10);

        $this->assertSame(
            "set static-route '0.0.0.0/0' nexthop gateway address '192.168.1.254' on priority 10 && save config",
            $ops->capturedCommand
        );
    }

    public function testRemoveStaticRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeStaticRoute('10.0.0.0/8');

        $this->assertSame(
            "delete static-route '10.0.0.0/8' && save config",
            $ops->capturedCommand
        );
    }

    public function testSetDefaultGateway(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDefaultGateway('192.168.1.254');

        $this->assertSame(
            "set static-route default nexthop gateway address '192.168.1.254' on && save config",
            $ops->capturedCommand
        );
    }

    public function testGetClusterStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getClusterStatus();

        $this->assertSame('show cluster', $ops->capturedCommand);
    }

    public function testGetFirewallStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getFirewallStatus();

        $this->assertSame('show firewall all', $ops->capturedCommand);
    }

    public function testGetUsers(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getUsers();

        $this->assertSame('show user all', $ops->capturedCommand);
    }

    public function testCreateUser(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createUser('admin', 'Str0ng!Pass');

        $this->assertSame(
            "add user 'admin' && set user 'admin' password 'Str0ng!Pass' && save config",
            $ops->capturedCommand
        );
    }

    public function testDeleteUser(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteUser('admin');

        $this->assertSame(
            "delete user 'admin' && save config",
            $ops->capturedCommand
        );
    }

    public function testGetSnmpCommunities(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSnmpCommunities();

        $this->assertSame('show snmp community all', $ops->capturedCommand);
    }

    public function testAddSnmpCommunity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addSnmpCommunity('public');

        $this->assertSame(
            "set snmp community 'public' && save config",
            $ops->capturedCommand
        );
    }

    public function testDeleteSnmpCommunity(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteSnmpCommunity('public');

        $this->assertSame(
            "delete snmp community 'public' && save config",
            $ops->capturedCommand
        );
    }

    public function testSetSnmpAgentEnable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSnmpAgent(true);

        $this->assertSame('set snmp agent on && save config', $ops->capturedCommand);
    }

    public function testSetSnmpAgentDisable(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSnmpAgent(false);

        $this->assertSame('set snmp agent off && save config', $ops->capturedCommand);
    }

    public function testGetSyslogConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSyslogConfig();

        $this->assertSame('show syslog all', $ops->capturedCommand);
    }

    public function testSetSyslogServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSyslogServer('192.168.1.100');

        $this->assertSame(
            "set syslog server '192.168.1.100'"
            . " && set syslog server '192.168.1.100' facility 'local0'"
            . " && save config",
            $ops->capturedCommand
        );
    }

    public function testGetTasks(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getTasks();

        $this->assertSame('show task all', $ops->capturedCommand);
    }

    public function testGetGatewayStatus(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getGatewayStatus();

        $this->assertSame('show gateway all', $ops->capturedCommand);
    }

    public function testPing(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('8.8.8.8');

        $this->assertSame("ping '8.8.8.8' count 5 size 64", $ops->capturedCommand);
    }

    public function testPingCustom(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('10.0.0.1', 10, 1500);

        $this->assertSame("ping '10.0.0.1' count 10 size 1500", $ops->capturedCommand);
    }

    public function testTraceroute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->traceroute('google.com');

        $this->assertSame("traceroute 'google.com'", $ops->capturedCommand);
    }

    public function testGetConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getConfig();

        $this->assertSame('show configuration', $ops->capturedCommand);
    }

    public function testGetLicenses(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLicenses();

        $this->assertSame('show license all', $ops->capturedCommand);
    }

    public function testGetLogs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLogs(200);

        $this->assertSame('show log last 200', $ops->capturedCommand);
    }

    public function testExec(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exec('show cluster members');

        $this->assertSame('show cluster members', $ops->capturedCommand);
    }

    public function testCheckPointOpsReturnsRemoteCommandOutput(): void
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
        $ops->getRoutes();

        $this->assertCount(3, $ops->capturedCommands);
        $this->assertSame('show version all', $ops->capturedCommands[0]);
        $this->assertSame('show interface all', $ops->capturedCommands[1]);
        $this->assertSame('show route', $ops->capturedCommands[2]);
    }

    public function testSetHostnameEscapesSpaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setHostname('Check Point GW 1');

        $this->assertSame("set hostname 'Check Point GW 1' && save config", $ops->capturedCommand);
    }

    public function testSetInterfaceCommentEscapesSpaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceComment('eth2', 'Backup link to DC');

        $this->assertSame(
            "set interface 'eth2' comments 'Backup link to DC' && save config",
            $ops->capturedCommand
        );
    }

    public function testAddSnmpCommunityEscapesSpecialChars(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addSnmpCommunity('monitor@office');

        $this->assertSame(
            "set snmp community 'monitor@office' && save config",
            $ops->capturedCommand
        );
    }
}
