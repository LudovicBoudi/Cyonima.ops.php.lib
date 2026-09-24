<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\Network\PaloAltoOps;
use Cyonima\Ops\RemoteCommandOutput;
use PHPUnit\Framework\TestCase;

final class PaloAltoOpsTest extends TestCase
{
    private function createOpsWithCapture(): object
    {
        return new class() extends PaloAltoOps {
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

    public function testGetSystemInfo(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSystemInfo();

        $this->assertSame('show system info', $ops->capturedCommand);
    }

    public function testGetVersion(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getVersion();

        $this->assertSame('show system info | match sw-version', $ops->capturedCommand);
    }

    public function testGetUptime(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getUptime();

        $this->assertSame('show system uptime', $ops->capturedCommand);
    }

    public function testGetResources(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getResources();

        $this->assertSame('show system resources', $ops->capturedCommand);
    }

    public function testGetClock(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getClock();

        $this->assertSame('show clock', $ops->capturedCommand);
    }

    public function testSetHostname(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setHostname('fw-primary');

        $this->assertSame("configure && set deviceconfig system hostname 'fw-primary' && commit", $ops->capturedCommand);
    }

    public function testSetDomainName(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDomainName('example.com');

        $this->assertSame("configure && set deviceconfig system domain 'example.com' && commit", $ops->capturedCommand);
    }

    public function testSetBanner(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setBanner('Authorized access only');

        $this->assertSame("configure && set deviceconfig system login banner 'Authorized access only' && commit", $ops->capturedCommand);
    }

    public function testSetDnsServers(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDnsServers(['8.8.8.8', '8.8.4.4']);

        $this->assertSame(
            "configure"
            . " && set deviceconfig system dns-setting servers '8.8.8.8'"
            . " && set deviceconfig system dns-setting servers '8.8.4.4'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testSetNtpServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setNtpServer('pool.ntp.org');

        $this->assertSame("configure && set deviceconfig system ntp-servers primary-ntp-server 'pool.ntp.org' && commit", $ops->capturedCommand);
    }

    public function testSetDefaultGateway(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setDefaultGateway('192.168.1.1');

        $this->assertSame("configure && set deviceconfig system default-gateway '192.168.1.1' && commit", $ops->capturedCommand);
    }

    public function testReboot(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->reboot();

        $this->assertSame('request system reboot', $ops->capturedCommand);
    }

    public function testShutdown(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->shutdown();

        $this->assertSame('request system shutdown', $ops->capturedCommand);
    }

    public function testGetInterfaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaces();

        $this->assertSame('show interfaces all', $ops->capturedCommand);
    }

    public function testGetInterfaceDetail(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getInterfaceDetail('ethernet1/1');

        $this->assertSame("show interface 'ethernet1/1'", $ops->capturedCommand);
    }

    public function testSetInterfaceIp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceIp('ethernet1/1', '10.0.0.1/24');

        $this->assertSame(
            "configure && set network interface ethernet 'ethernet1/1'"
            . " layer3 ip '10.0.0.1/24'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testRemoveInterfaceIp(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeInterfaceIp('ethernet1/1');

        $this->assertSame(
            "configure && delete network interface ethernet 'ethernet1/1'"
            . " layer3 ip"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceZone(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceZone('ethernet1/1', 'trust');

        $this->assertSame(
            "configure && set network interface ethernet 'ethernet1/1'"
            . " zone 'trust'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testSetInterfaceComment(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceComment('ethernet1/1', 'Link to core');

        $this->assertSame(
            "configure && set network interface ethernet 'ethernet1/1'"
            . " comment 'Link to core'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testCreateZone(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createZone('dmz', 'layer3', ['ethernet1/3', 'ethernet1/4']);

        $this->assertSame(
            "configure && set zone 'dmz' network layer3"
            . " && set zone 'dmz' network layer3 'ethernet1/3'"
            . " && set zone 'dmz' network layer3 'ethernet1/4'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testGetRoutes(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getRoutes();

        $this->assertSame('show routing route', $ops->capturedCommand);
    }

    public function testGetRouteForPrefix(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getRouteForPrefix('10.0.0.0/8');

        $this->assertSame("show routing route '10.0.0.0/8'", $ops->capturedCommand);
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
        $ops->addStaticRoute('to-datacenter', '10.0.0.0/8', '192.168.1.1');

        $this->assertSame(
            "configure"
            . " && set network virtual-router 'default'"
            . " routing-table ip static-route 'to-datacenter'"
            . " nexthop ip-address '192.168.1.1'"
            . " destination '10.0.0.0/8'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testAddStaticRouteCustomVr(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addStaticRoute('to-internet', '0.0.0.0/0', '192.168.1.254', 'vr-internet');

        $this->assertSame(
            "configure"
            . " && set network virtual-router 'vr-internet'"
            . " routing-table ip static-route 'to-internet'"
            . " nexthop ip-address '192.168.1.254'"
            . " destination '0.0.0.0/0'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testRemoveStaticRoute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->removeStaticRoute('to-datacenter');

        $this->assertSame(
            "configure"
            . " && delete network virtual-router 'default'"
            . " routing-table ip static-route 'to-datacenter'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testGetSecurityRules(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSecurityRules();

        $this->assertSame('show rulebase security rules', $ops->capturedCommand);
    }

    public function testAddSecurityRuleMinimal(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addSecurityRule('Allow-Outbound', 'allow');

        $this->assertSame(
            "configure"
            . " && set rulebase security rules 'Allow-Outbound' action 'allow'"
            . " && set rulebase security rules 'Allow-Outbound' from 'any'"
            . " && set rulebase security rules 'Allow-Outbound' to 'any'"
            . " && set rulebase security rules 'Allow-Outbound' source 'any'"
            . " && set rulebase security rules 'Allow-Outbound' destination 'any'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testAddSecurityRuleFull(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addSecurityRule(
            name: 'Allow-SSH',
            action: 'allow',
            from: 'untrust',
            to: 'trust',
            source: '10.0.0.0/8',
            destination: '192.168.1.10/32',
            application: 'ssh',
            service: 'application-default',
            description: 'Allow SSH from corp net'
        );

        $this->assertSame(
            "configure"
            . " && set rulebase security rules 'Allow-SSH' action 'allow'"
            . " && set rulebase security rules 'Allow-SSH' from 'untrust'"
            . " && set rulebase security rules 'Allow-SSH' to 'trust'"
            . " && set rulebase security rules 'Allow-SSH' source '10.0.0.0/8'"
            . " && set rulebase security rules 'Allow-SSH' destination '192.168.1.10/32'"
            . " && set rulebase security rules 'Allow-SSH' application 'ssh'"
            . " && set rulebase security rules 'Allow-SSH' service 'application-default'"
            . " && set rulebase security rules 'Allow-SSH' description 'Allow SSH from corp net'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testDeleteSecurityRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteSecurityRule('Allow-Outbound');

        $this->assertSame(
            "configure && delete rulebase security rules 'Allow-Outbound' && commit",
            $ops->capturedCommand
        );
    }

    public function testDisableSecurityRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->disableSecurityRule('Allow-Outbound');

        $this->assertSame(
            "configure && set rulebase security rules 'Allow-Outbound' disabled yes && commit",
            $ops->capturedCommand
        );
    }

    public function testEnableSecurityRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->enableSecurityRule('Allow-Outbound');

        $this->assertSame(
            "configure && delete rulebase security rules 'Allow-Outbound' disabled && commit",
            $ops->capturedCommand
        );
    }

    public function testMoveSecurityRuleTop(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->moveSecurityRule('Allow-SSH', 'top');

        $this->assertSame(
            "configure && move rulebase security rules 'Allow-SSH' 'top' && commit",
            $ops->capturedCommand
        );
    }

    public function testMoveSecurityRuleBefore(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->moveSecurityRule('Allow-SSH', 'before', 'Deny-All');

        $this->assertSame(
            "configure && move rulebase security rules 'Allow-SSH' 'before' 'Deny-All' && commit",
            $ops->capturedCommand
        );
    }

    public function testGetNatRules(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getNatRules();

        $this->assertSame('show rulebase nat rules', $ops->capturedCommand);
    }

    public function testAddNatRuleMinimal(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addNatRule('NAT-Internet');

        $this->assertSame(
            "configure"
            . " && set rulebase nat rules 'NAT-Internet' nat-type 'ipv4'"
            . " && set rulebase nat rules 'NAT-Internet' from 'any'"
            . " && set rulebase nat rules 'NAT-Internet' to 'any'"
            . " && set rulebase nat rules 'NAT-Internet' source 'any'"
            . " && set rulebase nat rules 'NAT-Internet' destination 'any'"
            . " && set rulebase nat rules 'NAT-Internet' service 'any'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testAddNatRuleFull(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->addNatRule(
            name: 'NAT-Web',
            natType: 'ipv4',
            from: 'untrust',
            to: 'trust',
            source: 'any',
            destination: '203.0.113.10',
            service: 'tcp-443',
            sourceTranslation: 'dynamic-ip-and-port',
            destinationTranslation: '192.168.1.10'
        );

        $this->assertSame(
            "configure"
            . " && set rulebase nat rules 'NAT-Web' nat-type 'ipv4'"
            . " && set rulebase nat rules 'NAT-Web' from 'untrust'"
            . " && set rulebase nat rules 'NAT-Web' to 'trust'"
            . " && set rulebase nat rules 'NAT-Web' source 'any'"
            . " && set rulebase nat rules 'NAT-Web' destination '203.0.113.10'"
            . " && set rulebase nat rules 'NAT-Web' service 'tcp-443'"
            . " && set rulebase nat rules 'NAT-Web' source-translation type 'dynamic-ip-and-port'"
            . " && set rulebase nat rules 'NAT-Web' destination-translation translated-address '192.168.1.10'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testDeleteNatRule(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteNatRule('NAT-Internet');

        $this->assertSame(
            "configure && delete rulebase nat rules 'NAT-Internet' && commit",
            $ops->capturedCommand
        );
    }

    public function testGetAddresses(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getAddresses();

        $this->assertSame('show shared address', $ops->capturedCommand);
    }

    public function testCreateAddress(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createAddress('web-server', 'ip-netmask', '10.0.0.10/32');

        $this->assertSame(
            "configure && set shared address 'web-server' 'ip-netmask' '10.0.0.10/32' && commit",
            $ops->capturedCommand
        );
    }

    public function testDeleteAddress(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteAddress('web-server');

        $this->assertSame(
            "configure && delete shared address 'web-server' && commit",
            $ops->capturedCommand
        );
    }

    public function testGetServices(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getServices();

        $this->assertSame('show shared service', $ops->capturedCommand);
    }

    public function testCreateService(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createService('tcp-8080', 'tcp', '8080');

        $this->assertSame(
            "configure && set shared service 'tcp-8080' protocol 'tcp' port '8080' && commit",
            $ops->capturedCommand
        );
    }

    public function testDeleteService(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->deleteService('tcp-8080');

        $this->assertSame(
            "configure && delete shared service 'tcp-8080' && commit",
            $ops->capturedCommand
        );
    }

    public function testGetSystemLogs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSystemLogs();

        $this->assertSame('show log system rows 100', $ops->capturedCommand);
    }

    public function testGetTrafficLogs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getTrafficLogs(50);

        $this->assertSame('show log traffic rows 50', $ops->capturedCommand);
    }

    public function testGetThreatLogs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getThreatLogs(200);

        $this->assertSame('show log threat rows 200', $ops->capturedCommand);
    }

    public function testSetSyslogServer(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setSyslogServer('syslog-pa', '192.168.1.100');

        $this->assertSame(
            "configure"
            . " && set shared log-settings syslog 'syslog-pa'"
            . " server '192.168.1.100'"
            . " facility 'local0'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testCommit(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->commit();

        $this->assertSame('commit', $ops->capturedCommand);
    }

    public function testCommitWithDescription(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->commit('Added DNS servers');

        $this->assertSame("commit description 'Added DNS servers'", $ops->capturedCommand);
    }

    public function testCommitForce(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->commitForce();

        $this->assertSame('commit force', $ops->capturedCommand);
    }

    public function testCommitForceWithDescription(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->commitForce('Emergency rule update');

        $this->assertSame("commit force description 'Emergency rule update'", $ops->capturedCommand);
    }

    public function testShowChanges(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->showChanges();

        $this->assertSame('show config diff', $ops->capturedCommand);
    }

    public function testDiscardConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->discardConfig();

        $this->assertSame('configure && discard && exit', $ops->capturedCommand);
    }

    public function testSaveConfig(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->saveConfig();

        $this->assertSame('save config', $ops->capturedCommand);
    }

    public function testPing(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('8.8.8.8');

        $this->assertSame("ping host '8.8.8.8' count 5", $ops->capturedCommand);
    }

    public function testPingCustomCount(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->ping('10.0.0.1', 10);

        $this->assertSame("ping host '10.0.0.1' count 10", $ops->capturedCommand);
    }

    public function testTraceroute(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->traceroute('google.com');

        $this->assertSame("traceroute 'google.com'", $ops->capturedCommand);
    }

    public function testGetSessions(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getSessions();

        $this->assertSame('show session all', $ops->capturedCommand);
    }

    public function testGetAdmins(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getAdmins();

        $this->assertSame('show admins', $ops->capturedCommand);
    }

    public function testGetJobs(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getJobs();

        $this->assertSame('show jobs all', $ops->capturedCommand);
    }

    public function testGetLicenses(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->getLicenses();

        $this->assertSame('show licenses', $ops->capturedCommand);
    }

    public function testExec(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->exec('show interface management');

        $this->assertSame('show interface management', $ops->capturedCommand);
    }

    public function testPaloAltoOpsReturnsRemoteCommandOutput(): void
    {
        $ops = $this->createOpsWithCapture();
        $result = $ops->getSystemInfo();

        $this->assertInstanceOf(RemoteCommandOutput::class, $result);
    }

    public function testMultipleCallsAccumulateCommands(): void
    {
        $ops = $this->createOpsWithCapture();

        $ops->getSystemInfo();
        $ops->getVersion();
        $ops->getInterfaces();

        $this->assertCount(3, $ops->capturedCommands);
        $this->assertSame('show system info', $ops->capturedCommands[0]);
        $this->assertSame('show system info | match sw-version', $ops->capturedCommands[1]);
        $this->assertSame('show interfaces all', $ops->capturedCommands[2]);
    }

    public function testSetInterfaceCommentEscapesSpaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->setInterfaceComment('ethernet1/2', 'DMZ interface v2');

        $this->assertSame(
            "configure && set network interface ethernet 'ethernet1/2'"
            . " comment 'DMZ interface v2'"
            . " && commit",
            $ops->capturedCommand
        );
    }

    public function testCreateZoneWithoutInterfaces(): void
    {
        $ops = $this->createOpsWithCapture();
        $ops->createZone('vwire-zone', 'virtual-wire');

        $this->assertSame(
            "configure && set zone 'vwire-zone' network virtual-wire && commit",
            $ops->capturedCommand
        );
    }
}
