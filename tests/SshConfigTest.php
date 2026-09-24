<?php

declare(strict_types=1);

namespace Cyonima\Ops\Tests;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\Linux\UbuntuOps;
use PHPUnit\Framework\TestCase;

final class SshConfigTest extends TestCase
{
    private string $configFile;

    protected function setUp(): void
    {
        $this->configFile = tempnam(sys_get_temp_dir(), 'ssh_config_');
        $content = <<<'SSHCFG'
# Default config
Host *
    IdentityFile ~/.ssh/id_rsa

Host github.com
    HostName github.com
    User git
    Port 22
    IdentityFile ~/.ssh/id_ed25519

Host prod-*
    HostName 10.0.0.%h
    User admin
    Port 2222
    ProxyJump bastion.example.com

Host db-main
    HostName 192.168.1.50
    User deploy
    Port 5432

SSHCFG;
        file_put_contents($this->configFile, $content);
    }

    protected function tearDown(): void
    {
        unlink($this->configFile);
    }

    public function testParseSshConfigAppliesWildcardToUnknownHost(): void
    {
        // Host * matches any host, so identityFile should be picked up
        $config = AbstractOps::parseSshConfig('unknown-host', $this->configFile);
        $this->assertNull($config['hostname']);
        $this->assertNull($config['port']);
        $this->assertNull($config['user']);
        $this->assertStringContainsString('id_rsa', $config['identityFile']);
        $this->assertNull($config['proxyJump']);
    }

    public function testParseSshConfigExactMatch(): void
    {
        $config = AbstractOps::parseSshConfig('db-main', $this->configFile);
        $this->assertSame('192.168.1.50', $config['hostname']);
        $this->assertSame(5432, $config['port']);
        $this->assertSame('deploy', $config['user']);
    }

    public function testParseSshConfigWildcardHost(): void
    {
        $config = AbstractOps::parseSshConfig('prod-web', $this->configFile);
        $this->assertSame('10.0.0.prod-web', $config['hostname']);
        $this->assertSame(2222, $config['port']);
        $this->assertSame('admin', $config['user']);
        $this->assertSame('bastion.example.com', $config['proxyJump']);
    }

    public function testParseSshConfigMergesWildcardAndExact(): void
    {
        $config = AbstractOps::parseSshConfig('github.com', $this->configFile);

        // github.com matches both * (IdentityFile) and github.com (HostName, User, Port)
        // Exact match properties take precedence (already set first)
        $this->assertSame('github.com', $config['hostname']);
        $this->assertSame(22, $config['port']);
        $this->assertSame('git', $config['user']);
        // IdentityFile from * should also be picked up
        $this->assertStringContainsString('id_rsa', $config['identityFile']);
    }

    public function testParseSshConfigWithNonexistentFile(): void
    {
        $config = AbstractOps::parseSshConfig('test', '/nonexistent/config');
        $this->assertNull($config['hostname']);
        $this->assertNull($config['port']);
        $this->assertNull($config['user']);
    }

    public function testApplySshConfigSetsHostFromHostName(): void
    {
        $ops = new class() extends UbuntuOps {
            public array $capturedSettings = [];
            public function setHost(string $host): self
            {
                $this->capturedSettings['host'] = $host;
                return $this;
            }
            public function setSshPort(int $port): self
            {
                $this->capturedSettings['port'] = $port;
                return $this;
            }
            public function setProxy(string $proxy): self
            {
                $this->capturedSettings['proxy'] = $proxy;
                return $this;
            }
        };

        $ops->applySshConfig('db-main', $this->configFile);

        $this->assertSame('192.168.1.50', $ops->capturedSettings['host']);
        $this->assertSame(5432, $ops->capturedSettings['port']);
    }

    public function testApplySshConfigSetsProxyFromProxyJump(): void
    {
        $ops = new class() extends UbuntuOps {
            public array $capturedSettings = [];
            public function setHost(string $host): self
            {
                $this->capturedSettings['host'] = $host;
                return $this;
            }
            public function setSshPort(int $port): self
            {
                $this->capturedSettings['port'] = $port;
                return $this;
            }
            public function setProxy(string $proxy): self
            {
                $this->capturedSettings['proxy'] = $proxy;
                return $this;
            }
        };

        $ops->applySshConfig('prod-web', $this->configFile);

        $this->assertSame('bastion.example.com', $ops->capturedSettings['proxy']);
    }

    public function testStrictHostKeyCheckingFluent(): void
    {
        $ops = new UbuntuOps();
        $result = $ops->setStrictHostKeyChecking(true);
        $this->assertSame($ops, $result);
    }

    public function testGetHostFingerprintReturnsNullBeforeConnect(): void
    {
        $ops = new UbuntuOps();
        $this->assertNull($ops->getHostFingerprint());
    }

    public function testSetKnownHostsFileWithValidPath(): void
    {
        $knownHosts = tempnam(sys_get_temp_dir(), 'known_hosts_');
        file_put_contents($knownHosts, '');
        $ops = new UbuntuOps();
        $result = $ops->setKnownHostsFile($knownHosts);
        $this->assertSame($ops, $result);
        unlink($knownHosts);
    }

    public function testSetKnownHostsFileWithInvalidPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $ops = new UbuntuOps();
        $ops->setKnownHostsFile('/nonexistent/known_hosts');
    }

    public function testApplySshConfigWithNoConfigFileSetsHostOnly(): void
    {
        $ops = new class() extends UbuntuOps {
            public array $capturedSettings = [];
            public function setHost(string $host): self
            {
                $this->capturedSettings['host'] = $host;
                return $this;
            }
        };

        $ops->applySshConfig('myhost', '/nonexistent/config');

        $this->assertSame('myhost', $ops->capturedSettings['host']);
    }
}
