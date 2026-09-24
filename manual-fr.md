# Manuel complet — Cyonima OPS PHP Library

Dernière mise à jour : 1 juin 2026

Ce manuel est une documentation complète de la bibliothèque Cyonima OPS. Il fournit
des informations approfondies pour l'installation, la configuration, la compréhension
de l'architecture, l'utilisation des API, des exemples opérationnels, les bonnes
pratiques de sécurité, l'exécution des tests et l'intégration continue.

## Table des matières
- [1. Vue d'ensemble](#1-vue-densemble)
- [2. Installation et prérequis](#2-installation-et-prérequis)
- [3. Architecture et concepts clés](#3-architecture-et-concepts-clés)
- [4. Modules et API détaillées](#4-modules-et-api-détaillées)
- [5. Utilisation détaillée et exemples](#5-utilisation-détaillée-et-exemples)
- [6. Sécurité, validation et échappement](#6-sécurité-validation-et-échappement)
- [7. Analyse statique (PHPStan)](#7-analyse-statique-phpstan)
- [8. Tests, couverture et CI](#8-tests-couverture-et-ci)
- [9. Dépannage et diagnostics](#9-dépannage-et-diagnostics)
- [10. Contribuer et publier](#10-contribuer-et-publier)
- [11. Annexes](#11-annexes)

---

## 1. Vue d'ensemble

Cyonima OPS est une bibliothèque PHP de haut niveau destinée à l'automatisation
d'administration d'infrastructures via SSH/SCP/SFTP et WinRM. Elle encapsule les
opérations récurrentes (gestion des paquets, utilisateurs, services, firewall,
réseau, virtualisation, cloud, etc.) derrière des helpers typés et testables.

La bibliothèque privilégie :
- des API claires et typées (PHP 8.1+),
- l'échappement systématique des arguments,
- la séparation des responsabilités (ops par OS / module),
- la compatibilité PSR-3 pour le logging,
- les transfers via SCP et SFTP,
- la sécurisation des mots de passe (jamais exposés dans la table des processus).

### Arbre des modules

```
Cyonima\Ops\
├── AbstractOps              # Classe de base : SSH, SCP, SFTP, auth
├── RemoteCommandOutput      # Valeur de retour encapsulée
├── InputValidator           # Validation des entrées
├── Traits\
│   ├── UnixFileTrait        # Opérations fichier (Linux + BSD)
│   ├── Linux\
│   │   ├── LinuxServiceTrait
│   │   ├── LinuxUserTrait
│   │   └── LinuxSystemTrait
│   └── Bsd\
│       ├── BsdServiceTrait
│       ├── BsdUserTrait
│       └── BsdNetworkTrait
├── Linux\
│   ├── AbstractLinuxOps     # abstract, utilise les 4 traits
│   ├── UbuntuOps            # apt
│   ├── RedhatOps            # yum/dnf
│   ├── SuseOps              # zypper
│   ├── LinuxMintOps         # apt (hérite UbuntuOps)
│   └── ZorinOps             # apt (hérite UbuntuOps)
├── Bsd\
│   ├── AbstractBsdOps       # abstract, utilise les 4 traits
│   ├── FreeBsdOps           # pkg
│   └── OpenBsdOps           # pkg_add/pkg_delete
├── Windows\
│   ├── AbstractWindowsOps   # PowerShell over SSH
│   ├── WindowsOps           # implémentation concrète
│   ├── WindowsWinRmOps      # WinRM natif (SOAP/cURL)
│   ├── WinRmClient          # Client SOAP WinRM
│   ├── IISOps               # IIS manager
│   ├── SQLServerOps         # SQL Server queries
│   └── HyperVOps            # Hyper-V manager
├── MacOS\
│   └── MacOsOps             # brew, sysadminctl
├── Network\
│   ├── CiscoOps             # Cisco IOS/IOS-XE
│   ├── JuniperOps           # Juniper JunOS
│   ├── MikrotikOps          # MikroTik RouterOS
│   ├── HuaweiVrpOps         # Huawei VRP
│   ├── PaloAltoOps          # Palo Alto PAN-OS
│   ├── FortinetOps          # Fortinet FortiOS
│   └── CheckPointOps        # Check Point Gaia
├── Aws\
│   └── AwsOps               # AWS CLI via SSH
├── Azure\
│   └── AzureOps             # Azure CLI via SSH
├── Gcp\
│   └── GcpOps               # gcloud CLI via SSH
├── OpenStack\
│   └── OpenStackOps         # openstack CLI via SSH
├── Kvm\
│   └── KvmOps               # virsh CLI
├── Virtualbox\
│   └── VirtualboxOps        # VBoxManage CLI
├── Docker\
│   └── DockerOps            # conteneurs, compose, images
├── Database\
│   ├── AbstractDatabaseOps  # abstraite
│   ├── MySqlOps             # mysql, mysqldump
│   └── PostgreSqlOps        # psql, pg_dump, pg_restore
├── WebServer\
│   ├── AbstractWebServerOps # abstraite
│   ├── NginxOps             # nginx, vhosts, logs
│   └── ApacheOps            # apache2ctl, a2ensite
├── System\
│   ├── SystemOps            # inventaire (CPU, disque, etc.)
│   ├── CronOps              # gestion crontab
│   └── SshKeyOps            # authorized_keys
├── Kubernetes\
│   └── KubernetesOps        # kubectl, helm
├── LoadBalancer\
│   └── HaProxyOps           # stats HAProxy, serveurs
├── Ssl\
│   └── CertbotOps           # certificats Let's Encrypt
├── Backup\
│   └── RsyncOps             # rsync, sauvegardes
├── Vmware\
│   └── VmwareOps            # vim-cmd, esxcli (ESXi)
├── Kvm\
│   ├── AbstractKvmOps
│   └── KvmOps               # virsh CLI
├── Virtualbox\
│   └── VirtualboxOps        # VBoxManage
└── Proxmox\
    └── ProxmoxOps           # qm + pvesh CLI
```

## 2. Installation et prérequis

### Prérequis système

- PHP **8.1 ou supérieur** (requiert `Stringable` et `readonly` properties)
- Extensions PHP recommandées :
  - `ext-ssh2` — connexions SSH, SCP, SFTP
  - `ext-curl` — WinRM (SOAP over HTTPS)
  - `ext-mbstring` — manipulation de chaînes multi-octets
- Composer — gestion des dépendances

### Installation

```bash
composer require cyonima/ops-lib
```

Installer les dépendances de développement :

```bash
composer install --no-interaction
```

### Configuration minimale

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setHost('192.0.2.10')
    ->setCredentials('admin', 'password')
    ->setSshPort(22);
$ops->openConnection();
// ...
$ops->closeConnection();
```

## 3. Architecture et concepts clés

### 3.1 `AbstractOps`

Classe de base de toute la bibliothèque. Gère la connexion SSH, l'authentification,
l'exécution de commandes distantes et les transfers de fichiers.

#### Propriétés

| Propriété | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `DEFAULT_SSH_PORT` | `int` | `22` | Port SSH par défaut |
| `DEFAULT_SSH_TIMEOUT` | `int` | `30` | Timeout connexion (secondes) |

#### Méthodes de configuration

| Méthode | Retour | Description |
|---------|--------|-------------|
| `setHost(string $host)` | `self` | Définit la cible |
| `setSshPort(int $port)` | `self` | Port SSH (1-65535) |
| `setSshTimeout(int $seconds)` | `self` | Timeout connexion (min 1s) |
| `setCredentials(string $username, string $password)` | `self` | Auth par mot de passe |
| `setRsaAuthentication(string $username, string $privateKey, string $publicKey)` | `self` | Auth par clé RSA |
| `setAgentAuthentication(string $username)` | `self` | Auth par agent SSH |
| `setProxy(string $proxyHost)` | `self` | Jump host SSH |
| `unsetProxy()` | `self` | Désactive le proxy |
| `setLogger(LoggerInterface $logger)` | `self` | Logger PSR-3 personnalisé |
| `setStrictHostKeyChecking(bool $strict)` | `self` | Active/désactive la vérification stricte de la clé d'hôte |
| `setKnownHostsFile(string $path)` | `self` | Chemin vers le fichier known_hosts |
| `applySshConfig(string $host, ?string $configFile)` | `self` | Applique les réglages ~/.ssh/config pour un hôte |

L'ordre de priorité d'authentification est : **agent SSH → clé RSA → mot de passe**.

#### Connexion / déconnexion

| Méthode | Description |
|---------|-------------|
| `openConnection(): void` | Ouvre la connexion SSH (throw `ConnectionException`, `AuthenticationException`) |
| `closeConnection(): void` | Ferme la connexion |
| `isConnected(): bool` | Vérifie si une connexion est active |
| `getHostFingerprint(): ?string` | Empreinte SHA-1 de la clé d'hôte du serveur (disponible après connexion) |

#### SSH config

| Méthode | Description |
|---------|-------------|
| `parseSshConfig(string $host, ?string $configFile): array` (static) | Parse ~/.ssh/config et retourne les paramètres (hostname, port, user, identityFile, proxyJump) |
| `applySshConfig(string $host, ?string $configFile): self` | Applique les paramètres SSH config à l'instance courante |

#### Exécution de commandes

| Méthode | Retour | Description |
|---------|--------|-------------|
| `remoteExec(string $command)` | `RemoteCommandOutput` | Exécute une commande SSH |

#### Transfert de fichiers

| Méthode | Description |
|---------|-------------|
| `scpPut(string $local, string $remote, string $permissions = '0644')` | Envoi via SCP |
| `scpGet(string $remote, string $local)` | Réception via SCP |
| `sftpPut(string $local, string $remote)` | Envoi via SFTP |
| `sftpGet(string $remote, string $local)` | Réception via SFTP (streaming SSH2) |

> **Note SFTP** : le sous-système SFTP est initialisé à la première utilisation
> (`initializeSftp()`) et réutilisé pour les transfers suivants.

### 3.2 `RemoteCommandOutput`

Structure immuable encapsulant le résultat d'une commande distante.

```php
$result = $ops->remoteExec('uname -a');
echo $result->getStdout();       // string
echo $result->getStderr();       // string
echo $result->getExitCode();     // int
$result->isSuccessful();         // bool — exit === 0
$result->throwIfFailed();        // throw ExecutionException si échec
```

### 3.3 `InputValidator` et échappement

`InputValidator` fournit des méthodes de validation statiques :

| Méthode | Description |
|---------|-------------|
| `validateHost(string)` | Valide un hôte (IP ou hostname) |
| `validateUsername(string)` | Valide un nom d'utilisateur |
| `validatePassword(string, int $minLength)` | Longueur minimale |
| `validateSshPort(int)` | Port entre 1 et 65535 |
| `validateFileReadable(string)` | Fichier local lisible |
| `validateCommand(string, bool $strict)` | Détection d'injection shell |
| `validateVlanNumber(string)` | VLAN entre 1-4094 |
| `validateIpAddress(string, bool $allowCidr)` | Adresse IP valide |
| `sanitizeFilename(string)` | Nettoie un nom de fichier |

Fonctions d'échappement :

- `escapeShellArgument(string $s): string` — utilise `escapeshellarg()` pour shell POSIX
- `escapePowerShellArgument(string $s): string` — quote simple + doublage des quotes pour PowerShell

### 3.4 Logging (PSR-3)

`SimpleLogger` implémente PSR-3 et écrit sur `stderr` par défaut.
Vous pouvez injecter n'importe quel logger PSR-3 :

```php
$ops->setLogger(new class extends AbstractLogger { ... });
```

## 4. Modules et API détaillées

### 4.1 Linux (`AbstractLinuxOps`)

`AbstractLinuxOps` utilise 4 traits pour organiser ses méthodes :

#### LinuxServiceTrait — Gestion des services systemd

```php
$ops->systemctl('nginx', 'status');
$ops->systemctlWithPrivilege('nginx', 'restart', $sudoPassword);
$ops->systemctlWithPrivilegeNoPwd('nginx', 'start');
```

#### LinuxUserTrait — Gestion des utilisateurs

```php
$ops->addUser('jdoe', 'P@ssw0rd', 'staff');          // groupe optionnel
$ops->addUserWithPrivilege('jdoe', 'P@ssw0rd', $sudoPassword);
$ops->deleteUser('jdoe');
$ops->deleteUserWithPrivilege('jdoe', $sudoPassword);
$ops->changePassword('jdoe', 'NewP@ss');
$ops->changePasswordWithPrivilege('jdoe', 'NewP@ss', $sudoPassword);
```

#### LinuxSystemTrait — Processus, logs, réseau, firewall, sécurité

```php
// Processus
$ops->listProcesses();                                // ou listProcesses('apache')
$ops->killProcess(1234);                              // SIGTERM (kill -15)
$ops->killProcessByName('nginx');                     // pkill -f
$ops->getTopProcesses(10);

// Logs
$ops->readLogFile('/var/log/syslog', 100);            // tail -n 100
$ops->readJournalLog('nginx.service', 50);
$ops->tailJournalLog('nginx.service', 20);

// Réseau
$ops->getNetworkInterfaces();
$ops->getRoutingTable();
$ops->addNetworkRoute('10.0.0.0/8', '192.168.1.1', 'eth0');

// Firewall
$ops->getFirewallStatus();                            // ufw status / firewall-cmd
$ops->enableFirewall();
$ops->disableFirewall();
$ops->addFirewallRule('443', 'tcp');

// SELinux
$ops->getSelinuxStatus();
$ops->setSelinuxMode('Enforcing');

// AppArmor
$ops->getAppArmorStatus();
$ops->enforceAppArmorProfile('my-profile');
```

Le firewall détecte automatiquement `ufw` ou `firewall-cmd` sur la cible.

#### UnixFileTrait — Opérations fichier

```php
$ops->readFile('/etc/hosts');
$ops->writeFile('/tmp/test.txt', "contenu\n");
$ops->writeFileWithPrivilege('/etc/hosts', "127.0.0.1 localhost\n", $sudoPassword);
$ops->copyFile('/tmp/a', '/tmp/b');
$ops->copyFileWithPrivilege('/etc/a', '/etc/b', $sudoPassword);
$ops->moveFile('/tmp/a', '/tmp/b');
$ops->removeFile('/tmp/old.log');
$ops->removeFileWithPrivilege('/var/log/old.log', $sudoPassword);
$ops->makeDirectory('/app/data', '0750');
$ops->changePermissions('/app/data', '0755');
$ops->changeOwner('/app/data', 'www-data', 'www-data');
```

Les méthodes `writeFile` / `writeFileWithPrivilege` utilisent `base64` pour
transférer le contenu sans risque d'échappement.

#### Package management (abstract)

`AbstractLinuxOps` déclare des méthodes abstraites pour la gestion des paquets.
Chaque distribution concrète les implémente :

- `UbuntuOps`, `LinuxMintOps`, `ZorinOps` → `apt`
- `RedhatOps` → `yum` / `dnf`
- `SuseOps` → `zypper`

```php
// Exemple avec UbuntuOps
$ops->installPackage('nginx');
$ops->installPackageWithPrivilege('nginx', $sudoPassword);
$ops->uninstallPackage('apache2');
$ops->upgradePackages();                              // apt update && apt upgrade -y
```

#### executeWithSudo() — exécution sécurisée avec sudo

La méthode `executeWithSudo()` protège le mot de passe sudo en utilisant un fichier
temporaire avec permissions restreintes :

```php
// Au lieu de : echo password | sudo -S command  (visible dans `ps aux`)
// La méthode fait :
//   1. base64_encode(password) → fichier temporaire /tmp/._sudo_<random>
//   2. chmod 400 sur le fichier
//   3. sudo -S command < fichier
//   4. rm -f du fichier
```

### 4.2 Windows

#### PowerShell over SSH (`AbstractWindowsOps`, `WindowsOps`)

Toutes les commandes sont wrappées par `toPowerShellCommand()` :

```php
// En interne : powershell -NoProfile -NonInteractive -Command '<script échappé>'
```

Helpers disponibles :

```php
$win->readFile('C:\\temp\\log.txt');
$win->writeFile('C:\\temp\\out.txt', 'contenu');
$win->getProcesses();                                 // ConvertTo-Json
$win->killProcessById(1234);
$win->getEventLog('Application', 50);
$win->getNetworkInterfaces();
$win->getFirewallProfile();
$win->addFirewallRule('AllowSSH', 'description', 'Inbound', 'TCP', '22');
```

Modules Windows spécialisés :

| Classe | Description |
|--------|-------------|
| `WindowsOps` | Helpers génériques Windows |
| `IISOps` | Gestion IIS (sites, pools d'applications) |
| `SQLServerOps` | Exécution de requêtes SQL |
| `HyperVOps` | Gestion Hyper-V (création VM, etc.) |
| `WindowsWinRmOps` | WinRM natif (SOAP) |

```php
// Hyper-V
$hv = new HyperVOps();
$hv->createVM('vm1', 2048, 'C:\\vhd\\vm1.vhdx');
$hv->startVM('vm1');

// SQL Server
$sql = new SQLServerOps();
$sql->runQuery('localhost\\SQLEXPRESS', 'master', 'SELECT 1;');
```

Active Directory :

```php
$win->joinDomain('corp.example.local', 'corp\\admin', 'P@ssw0rd');
$win->createAdUser('jdoe', 'John Doe', 'P@ssw0rd', 'OU=Users,DC=corp,DC=local');
$win->addAdUserToGroup('jdoe', 'Domain Admins');
```

#### WinRM natif (`WinRmClient`, `WindowsWinRmOps`)

Pour les environnements sans accès SSH :

```php
use Cyonima\Ops\Windows\WinRmClient;
use Cyonima\Ops\Windows\WindowsWinRmOps;

$client = new WinRmClient('srv-windows.example.local', 'Administrator', 'password');
$client->setUseHttps(true);                           // WinRM over HTTPS (5986)
$winrm = new WindowsWinRmOps($client);
$version = $winrm->getWindowsVersion();
```

### 4.3 macOS (`MacOsOps`)

```php
$mac = new MacOsOps();
$mac->getMacOsVersion();                              // sw_vers -productVersion
$mac->installPackage('nginx');                        // brew install
$mac->manageService('nginx', 'start');                // brew services
$mac->addUser('jdoe', 'P@ssw0rd');                   // sysadminctl
$mac->getFirewallStatus();                            // /usr/libexec/ApplicationFirewall
$mac->enableFirewall();
```

### 4.4 Réseau (`Cyonima\Ops\Network`)

Sept classes pour l'automatisation des équipements réseau, toutes héritent de `AbstractOps`.
La connexion suit le même schéma pour chaque constructeur :

```php
use Cyonima\Ops\Network\CiscoOps;

$device = new CiscoOps();
$device->setHost('switch.example.local')
       ->setCredentials('admin', 'secret');
$device->openConnection();
$result = $device->getVersion();     // RemoteCommandOutput
```

#### 4.4.1 CiscoOps — Cisco IOS/IOS-XE (56 méthodes)

```php
// Consultation
$cisco->getHostname();
$cisco->getVersion();
$cisco->getRunningConfig();
$cisco->getInterfaces();
$cisco->getVlans();
$cisco->getIpRoute();
$cisco->getCdpNeighbors();
$cisco->getLldpNeighbors();

// Configuration VLAN
$cisco->createVlan(10, 'Users');
$cisco->deleteVlan(10);
$cisco->setInterfaceAccessVlan('GigabitEthernet0/1', 10);
$cisco->setInterfaceTrunk('GigabitEthernet0/24', '10,20', '1');

// Configuration interface
$cisco->setInterfaceDescription('GigabitEthernet0/1', 'Link to Core');
$cisco->setInterfaceIp('GigabitEthernet0/1', '192.168.1.1', '255.255.255.0');
$cisco->createVlanInterface(10, '192.168.10.1', '255.255.255.0');
$cisco->shutdownInterface('GigabitEthernet0/2');
$cisco->noShutdownInterface('GigabitEthernet0/2');

// Routage
$cisco->addStaticRoute('0.0.0.0', '0.0.0.0', '192.168.1.254');
$cisco->removeStaticRoute('10.0.0.0', '255.0.0.0', '192.168.1.1');
$cisco->setDefaultGateway('192.168.1.254');

// Sécurité
$cisco->enableSsh('domain.local');
$cisco->createUser('jdoe', 'P@ssw0rd', 15);
$cisco->addAclRule(100, 'deny', '10.0.0.0 0.0.0.255');
$cisco->applyAclInbound('GigabitEthernet0/1', '100');

// Services
$cisco->setNtpServer('pool.ntp.org');
$cisco->setSnmpCommunity('public', 'ro');
$cisco->setSyslogServer('192.168.1.100');
$cisco->saveConfig();

// Diagnostic
$cisco->ping('8.8.8.8');
$cisco->traceroute('8.8.8.8');
$cisco->getUptime();
$cisco->getLogs();
$cisco->getEnvironment();
$cisco->exec('show ip interface brief');
```

#### 4.4.2 JuniperOps — Juniper JunOS (57 méthodes)

```php
// Consultation
$junos->getHostname();
$junos->getVersion();
$junos->getConfiguration();
$junos->getConfigurationSection('interfaces');
$junos->getInterfaces();
$junos->getVlans();
$junos->getRouteTable();
$junos->getArpTable();
$junos->getLldpNeighbors();

// Configuration VLAN
$junos->createVlan('100', 'DMZ', 'vlan.100', '10.0.100.1/24');
$junos->deleteVlan('DMZ');
$junos->setInterfaceAccessVlan('ge-0/0/1', 'DMZ');
$junos->setInterfaceTrunk('ge-0/0/48', ['DMZ', 'Users']);

// Configuration interface
$junos->setInterfaceDescription('ge-0/0/1', 'Link to Core');
$junos->setInterfaceIp('ge-0/0/0', '192.168.1.1/24');
$junos->enableInterface('ge-0/0/2');
$junos->disableInterface('ge-0/0/2');

// Routage et sécurité
$junos->addStaticRoute('0.0.0.0/0', '192.168.1.254');
$junos->addFirewallFilter('protect', 'term1', 'reject', '10.0.0.0/8');
$junos->applyFilterInput('ge-0/0/0', 'protect');

// Commit et gestion
$junos->commit();
$junos->commitCheck();
$junos->rollback(1);
$junos->showChanges();
$junos->saveRescueConfig();

// Diagnostic
$junos->ping('8.8.8.8');
$junos->monitorInterface('ge-0/0/0', 2);
$junos->getAlarms();
$junos->getActiveUsers();
$junos->execOperational('show chassis hardware');
```

#### 4.4.3 MikrotikOps — MikroTik RouterOS (79 méthodes)

```php
// Système
$mt->getSystemIdentity();
$mt->setSystemIdentity('Core-Router');
$mt->getVersion();
$mt->getResources();
$mt->getHealth();
$mt->getUptime();
$mt->reboot();

// Interfaces
$mt->getInterfaces();
$mt->enableInterface('ether1');
$mt->disableInterface('ether2');
$mt->setInterfaceComment('ether1', 'WAN Link');

// IP et routage
$mt->getIpAddresses();
$mt->addIpAddress('192.168.1.1/24', 'ether1');
$mt->getRoutes();
$mt->addRoute('0.0.0.0/0', '192.168.1.254');
$mt->setDefaultGateway('192.168.1.254');

// VLAN et bridges
$mt->createVlan(10, 'VLAN10', 'ether2');
$mt->removeVlan('VLAN10');
$mt->createBridge('Bridge-LAN');
$mt->addBridgePort('Bridge-LAN', 'ether3');

// Firewall et NAT
$mt->getFirewallRules();
$mt->addFirewallRule('forward', 'drop', 'tcp', '0.0.0.0/0', '10.0.0.0/8');
$mt->addNatRule('srcnat', 'masquerade', '192.168.1.0/24');
$mt->getNatRules();

// Gestion des utilisateurs
$mt->getUsers();
$mt->createUser('jdoe', 'P@ssw0rd', 'full');

// Sauvegarde et configuration
$mt->exportConfig();
$mt->saveBackup('pre-upgrade');
$mt->loadBackup('pre-upgrade');

// Diagnostic
$mt->ping('8.8.8.8', 5, 64);
$mt->traceroute('8.8.8.8');
$mt->getLog(50);
$mt->getDhcpLeases();
$mt->getConnections();
$mt->exec('/interface wireless scan wlan1');
```

#### 4.4.4 HuaweiVrpOps — Huawei VRP (64 méthodes)

```php
// Consultation et vues
$huawei->enterSystemView();
$huawei->returnToUserView();
$huawei->getHostname();
$huawei->getVersion();
$huawei->getCurrentConfig();
$huawei->getInterfaces();
$huawei->getVlans();
$huawei->getIpRouteTable();

// VLAN
$huawei->createVlan(10, 'Users');
$huawei->createVlanBatch([10, 20, 30]);
$huawei->deleteVlan(10);
$huawei->setInterfaceAccessVlan('GigabitEthernet0/0/1', 10);
$huawei->setInterfaceTrunk('GigabitEthernet0/0/24', '10 20');
$huawei->setInterfaceHybrid('GigabitEthernet0/0/2', '10', '20 30');

// Interface
$huawei->setInterfaceDescription('GigabitEthernet0/0/1', 'Link to Core');
$huawei->setInterfaceIp('GigabitEthernet0/0/1', '192.168.1.1', '24');
$huawei->createVlanif(10, '192.168.10.1', '24');
$huawei->shutdownInterface('GigabitEthernet0/0/2');

// Routage et ACL
$huawei->addStaticRoute('0.0.0.0', '0.0.0.0', '192.168.1.254');
$huawei->setDefaultGateway('192.168.1.254');
$huawei->addAclRule(3000, 'deny', '10.0.0.0 0.255.255.255');
$huawei->applyAclInbound('GigabitEthernet0/0/1', 3000);
$huawei->enableSsh('P@ssw0rd');

// Services
$huawei->setNtpServer('pool.ntp.org');
$huawei->setSnmpCommunity('public', 'ro');
$huawei->setSyslogServer('192.168.1.100');
$huawei->enableLldp();
$huawei->createEthTrunk(1, ['GigabitEthernet0/0/1', 'GigabitEthernet0/0/2']);

// Diagnostic
$huawei->ping('8.8.8.8');
$huawei->getLldpNeighbors();
$huawei->getOspfNeighbors();
$huawei->getDeviceStatus();
$huawei->saveConfig();
$huawei->exec('display ip interface brief');
```

#### 4.4.5 PaloAltoOps — Palo Alto PAN-OS (56 méthodes)

```php
// Système
$pa->getSystemInfo();
$pa->getVersion();
$pa->getResources();
$pa->setHostname('PA-500');
$pa->setDomainName('example.local');
$pa->setDnsServers(['8.8.8.8', '8.8.4.4']);
$pa->setNtpServer('pool.ntp.org');

// Interfaces et zones
$pa->getInterfaces();
$pa->setInterfaceIp('ethernet1/1', '192.168.1.1/24');
$pa->setInterfaceZone('ethernet1/1', 'trust');
$pa->createZone('dmz', 'layer3', ['ethernet1/2']);

// Routage
$pa->getRoutes();
$pa->addStaticRoute('default-route', '0.0.0.0/0', '192.168.1.254');
$pa->removeStaticRoute('default-route');

// Règles de sécurité
$pa->getSecurityRules();
$pa->addSecurityRule('Allow-HTTP', 'allow', 'trust', 'untrust', '10.0.0.0/8', 'any', 'web-browsing', 'tcp-80');
$pa->deleteSecurityRule('Allow-HTTP');
$pa->disableSecurityRule('Old-Rule');

// NAT
$pa->getNatRules();
$pa->addNatRule('SNAT-LAN', 'ipv4', 'trust', 'untrust', '10.0.0.0/8', 'any');

// Objets
$pa->createAddress('web-server', 'ip-netmask', '10.0.1.10/32');
$pa->createService('HTTP', 'tcp', '80');

// Commit
$pa->commit('Règle HTTP ajoutée');
$pa->commitForce();
$pa->showChanges();
$pa->discardConfig();

// Diagnostic
$pa->ping('8.8.8.8');
$pa->getSystemLogs(50);
$pa->getTrafficLogs(50);
$pa->getSessions();
$pa->getLicenses();
$pa->exec('show system info');
```

#### 4.4.6 FortinetOps — Fortinet FortiOS (59 méthodes)

```php
// Système
$frt->getSystemStatus();
$frt->getVersion();
$frt->getPerformance();
$frt->getHaStatus();
$frt->setHostname('FortiGate-100D');
$frt->setDnsServers('8.8.8.8', '8.8.4.4');
$frt->setNtpServer('pool.ntp.org');

// Interfaces
$frt->getInterfaces();
$frt->setInterfaceIp('port1', '192.168.1.1', '255.255.255.0', 'ping https');
$frt->setInterfaceDescription('port1', 'WAN Link');
$frt->enableInterface('port2');
$frt->disableInterface('port3');
$frt->createVlan(10, 'port1', 10);

// Routage
$frt->getRoutes();
$frt->addStaticRoute(1, '0.0.0.0/0', '192.168.1.254', 'port1');
$frt->removeStaticRoute(1);
$frt->setDefaultGateway('192.168.1.254', 'port1');

// Politiques firewall
$frt->getPolicies();
$frt->addPolicy(1, 'Allow-Web', 'accept', 'port2', 'port1', 'all', 'all', 'HTTP');
$frt->deletePolicy(1);
$frt->enablePolicy(2);
$frt->disablePolicy(3);

// Objets et administration
$frt->createAddress('Web-Server', '10.0.1.10', '255.255.255.255');
$frt->createAddressGroup('Web-Servers', ['Web-Server']);
$frt->createService('Custom-HTTP', 'tcp', '8080');
$frt->createAdmin('jdoe', 'P@ssw0rd', 'super_admin');

// Sauvegarde
$frt->backupConfigTftp('192.168.1.100', 'fgt-backup.conf');
$frt->restoreConfigTftp('192.168.1.100', 'fgt-backup.conf');

// Diagnostic
$frt->ping('8.8.8.8');
$frt->getSessions();
$frt->getEventLogs(50);
$frt->getAttackLogs(50);
$frt->getDiagnostics();
$frt->exec('get system performance status');
```

#### 4.4.7 CheckPointOps — Check Point Gaia (43 méthodes)

```php
// Système
$cp->getVersion();
$cp->getHostname();
$cp->setHostname('GW-Core');
$cp->getAssetInfo();
$cp->setNtpServer('pool.ntp.org');
$cp->setDnsServers('8.8.8.8', '8.8.4.4');

// Interfaces
$cp->getInterfaces();
$cp->setInterfaceIp('eth0', '192.168.1.1', 24);
$cp->enableInterface('eth1');
$cp->disableInterface('eth1');
$cp->setInterfaceComment('eth0', 'WAN Link');
$cp->setInterfaceMtu('eth0', 1500);

// Routage
$cp->getRoutes();
$cp->addStaticRoute('0.0.0.0/0', '192.168.1.254');
$cp->removeStaticRoute('10.0.0.0/8');
$cp->setDefaultGateway('192.168.1.254');

// Cluster et firewall
$cp->getClusterStatus();
$cp->getFirewallStatus();
$cp->getGatewayStatus();

// Utilisateurs et SNMP
$cp->getUsers();
$cp->createUser('jdoe', 'P@ssw0rd');
$cp->addSnmpCommunity('public');
$cp->setSnmpAgent(true);

// Diagnostic
$cp->ping('8.8.8.8', 5, 64);
$cp->traceroute('8.8.8.8');
$cp->getLogs(50);
$cp->getTasks();
$cp->getConfig();
$cp->getLicenses();
$cp->exec('show asset all');
```

Toutes les méthodes retournent `RemoteCommandOutput`.

### 4.5 BSD (`AbstractBsdOps`)

Réutilise `UnixFileTrait` (fichiers), `BsdServiceTrait`, `BsdUserTrait`, `BsdNetworkTrait`.

#### FreeBSD

```php
$freebsd = new FreeBsdOps();
$freebsd->installPackage('nginx');                    // pkg install -y
$freebsd->uninstallPackage('nginx');                  // pkg delete
$freebsd->upgradePackages();                          // pkg upgrade
$freebsd->manageService('nginx', 'start');            // service nginx start
$freebsd->addUser('jdoe', 'P@ssw0rd');                // pw useradd
$freebsd->getFirewallStatus();                        // pfctl -s info
$freebsd->addFirewallRule('block all');               // echo ... | pfctl -f -
```

#### OpenBSD

```php
$openbsd = new OpenBsdOps();
$openbsd->installPackage('nginx');                    // pkg_add
$openbsd->uninstallPackage('nginx');                  // pkg_delete
$openbsd->manageService('httpd', 'status');           // service httpd status
```

### 4.6 Cloud — Azure

```php
$azure = new AzureOps();
$azure->loginWithServicePrincipal($tenantId, $clientId, $clientSecret);
$azure->setSubscription('sub-123');
$azure->createResourceGroup('prod-rg', 'westeurope');
$azure->createVirtualMachine('web-01', 'prod-rg', 'Standard_B2s', '...');
$azure->listVirtualMachines('prod-rg');
```

### 4.7 Cloud — OpenStack

```php
$openstack = new OpenStackOps();
$openstack->setOpenStackAuthentication($authUrl, $project, $user, $password);
$openstack->authenticate();
$openstack->createServer('web-01', 'Ubuntu20.04', 'm1.small', 'private-net', 'ssh-key');
$openstack->listServers();
```

### 4.8 Cloud — GCP

```php
$gcp = new GcpOps();
$gcp->authenticateWithServiceAccountKey('/tmp/sa-key.json');
$gcp->setProject('my-project');
$gcp->createInstance('web-01', 'europe-west1-b', 'e2-medium',
                     'ubuntu-2004-lts', 'ubuntu-os-cloud', 'default');
```

### 4.9 Cloud — AWS

```php
$aws = new AwsOps();
$aws->setProfile('prod');
$aws->setRegion('eu-west-1');
$aws->configureCredentials($accessKey, $secretKey, 'eu-west-1');
$aws->createInstance('web-01', 'ami-1234', 't3.micro', 'subnet-xxx', 'my-key', 'sg-yyy');
```

### 4.10 Virtualisation — KVM/libvirt

```php
$kvm = new KvmOps();
$kvm->getVirshVersion();
$kvm->createVm('vm01', '2', '2048', '/var/lib/libvirt/images/vm01.qcow2');
$kvm->startVm('vm01');
$kvm->getVmStatus('vm01');
$kvm->setVmCpu('vm01', '4');
$kvm->setVmMemory('vm01', '8192');
$kvm->listVMs();
$kvm->stopVm('vm01');
```

### 4.11 Virtualisation — VirtualBox

```php
$vbox = new VirtualboxOps();
$vbox->getVirtualboxVersion();
$vbox->createVm('vm01', 'Ubuntu_64', '2048', '2');
$vbox->createAndAttachDisk('vm01', '/vhd/disk.vdi', '40000');
$vbox->startVm('vm01', 'headless');
$vbox->listRunningVMs();
$vbox->cloneVm('vm01', 'vm01-clone', 'full');
$vbox->powerOffVm('vm01');
```

### 4.12 Virtualisation — Proxmox

```php
$proxmox = new ProxmoxOps();
$proxmox->getProxmoxVersion();                        // pvesh get /version
$proxmox->listNodes();                                // pvesh get /nodes
$proxmox->listVMs();                                  // qm list
$proxmox->createVm('100', 'vm01', '2048', '2', 'local:0');
$proxmox->startVm('100');                             // qm start
$proxmox->getVmStatus('100');                         // qm status
$proxmox->setVmCpu('100', '4');                       // qm set --cores
$proxmox->setVmMemory('100', '8192');                 // qm set --memory
$proxmox->createSnapshot('100', 'snap-pre-upgrade');  // qm snapshot
$proxmox->listSnapshots('100');                       // qm listsnapshot
$proxmox->cloneVm('100', '101', 'vm01-clone');        // qm clone
$proxmox->stopVm('100');                              // qm shutdown
$proxmox->killVm('100');                              // qm stop (force)
$proxmox->deleteVm('100');                            // qm destroy
```

## 5. Utilisation détaillée et exemples

### 5.1 Connexion SSH — modes d'authentification

#### Mot de passe

```php
$ops->setCredentials('admin', 'secret');
$ops->openConnection();
```

#### Clé RSA

```php
$ops->setRsaAuthentication('admin', '/home/user/.ssh/id_rsa', '/home/user/.ssh/id_rsa.pub');
$ops->openConnection();
```

#### Agent SSH

```php
$ops->setAgentAuthentication('admin');
$ops->openConnection();   // utilise ssh2_auth_agent()
```

### 5.2 Jump host (proxy SSH)

```php
$ops->setHost('db.internal.example.local');
$ops->setProxy('bastion.example.local');              // tunnel via bastion
$ops->openConnection();                               // ssh2_tunnel() vers la cible finale
```

### 5.3 Timeout configurable

```php
$ops->setSshTimeout(10);                              // abandon après 10s
$ops->openConnection();
$ops->isConnected();                                  // bool
```

### 5.4 Intégration SSH config

```php
// Parser ~/.ssh/config et appliquer les réglages pour un hôte
$config = AbstractOps::parseSshConfig('prod-web');
// Retourne : ['hostname' => '10.0.0.prod-web', 'port' => 2222, 'user' => 'admin', ...]

// Appliquer les réglages SSH config directement à une instance
$ops->applySshConfig('db-main');
// Configure host, port, user et proxy depuis ~/.ssh/config
$ops->openConnection();
```

### 5.5 Vérification d'empreinte de clé d'hôte

```php
// Activer la vérification stricte (optionnel — nécessite un fichier known_hosts)
$ops->setStrictHostKeyChecking(true);
$ops->setKnownHostsFile('/home/user/.ssh/known_hosts');
$ops->openConnection();

// Récupérer l'empreinte (toujours disponible après connexion)
$fingerprint = $ops->getHostFingerprint();
echo "Empreinte du serveur : $fingerprint";
```

### 5.6 Transfert de fichiers

### 5.4 Transfert de fichiers

```php
// SCP (via ssh2_scp_send / ssh2_scp_recv)
$ops->scpPut('/local/config.yaml', '/remote/config.yaml', '0644');
$ops->scpGet('/remote/backup.sql', '/local/backup.sql');

// SFTP (via ssh2_sftp, streaming)
$ops->sftpPut('/local/data.csv', '/remote/imports/data.csv');
$ops->sftpGet('/remote/logs/app.log', '/local/app.log');

// Par commande (base64) — pour fichiers textes
$ops->writeFile('/remote/config.php', "<?php\nreturn ['key'=>'value'];\n");
$content = $ops->readFile('/remote/config.php');
```

### 5.5 Gestion des erreurs

```php
use Cyonima\Ops\Exception\ConnectionException;
use Cyonima\Ops\Exception\AuthenticationException;
use Cyonima\Ops\Exception\ExecutionException;

try {
    $ops->openConnection();
    $result = $ops->remoteExec('ls /nonexistent');
    $result->throwIfFailed();                         // ExecutionException si exit != 0
} catch (ConnectionException $e) {
    echo 'Connexion impossible : ' . $e->getMessage();
} catch (AuthenticationException $e) {
    echo 'Authentification échouée : ' . $e->getMessage();
} catch (ExecutionException $e) {
    echo 'Commande en échec : ' . $e->getMessage();
    echo $result->getStderr();                        // Sortie d'erreur distante
}
```

## 6. Sécurité, validation et échappement

### 6.1 Principe général

Tous les arguments passés aux commandes distantes sont échappés via
`escapeShellArgument()` (shell POSIX) ou `escapePowerShellArgument()` (PowerShell).
Ne **jamais** concaténer des données utilisateur non échappées.

### 6.2 Injection shell

`InputValidator::validateCommand(string, bool $strict)` détecte :

- `; & | \` $ < > ( )` — opérateurs shell
- `${ }` — substitution de variables
- `` ` ` `` — substitution de commande
- `%0x` — encodage URL d'injection

```php
// ✅ Sûr
InputValidator::validateCommand('ls -la /tmp', true);   // passe

// ❌ Lève InvalidArgumentException
InputValidator::validateCommand('rm -rf /; echo hacked', true);
```

### 6.3 Sécurisation sudo

La méthode `executeWithSudo()` n'expose jamais le mot de passe dans `ps aux`.
Elle utilise un fichier temporaire protégé :

```php
// 1. base64_encode(password) → /tmp/._sudo_<random>
// 2. chmod 400
// 3. sudo -S command < tmpfile
// 4. rm -f tmpfile
```

### 6.4 Bonnes pratiques

- Préférez l'authentification par clé SSH plutôt que mot de passe
- Ne commitez jamais de mots de passe en clair (utilisez des variables d'environnement
  ou un gestionnaire de secrets : Vault, AWS Secrets Manager, etc.)
- Pour les opérations critiques (suppression de VM, changement de firewall),
  ajoutez une validation humaine ou un environnement de staging
- Appliquez le principe du moindre privilège sur les comptes sudo distants

## 7. Analyse statique (PHPStan)

Le projet est configuré pour PHPStan niveau 6.

```bash
composer analyze
# ou
vendor/bin/phpstan analyse
```

La configuration (`phpstan.neon`) ignore les fonctions `ssh2_*` (extension non
installée en développement) et les propriétés non typées des ressources SSH/SFTP.

## 8. Tests, couverture et CI

### Exécution des tests

```bash
vendor/bin/phpunit
```

### Écriture de tests

Les tests mockent `remoteExec()` via des classes anonymes :

```php
final class UbuntuOpsTest extends TestCase
{
    public function testInstallPackageUsesApt(): void
    {
        $ops = new class() extends UbuntuOps {
            public string $capturedCommand = '';
            public function remoteExec(string $command): RemoteCommandOutput
            {
                $this->capturedCommand = $command;
                return new RemoteCommandOutput('', '', 0);
            }
        };

        $result = $ops->installPackage('nginx');

        $this->assertStringContainsString('apt install -y', $ops->capturedCommand);
        $this->assertSame(0, $result->getExitCode());
    }
}
```

### CI (GitHub Actions)

Workflow : `.github/workflows/phpunit.yml`

- PHP 8.1 / 8.2 / 8.3
- `composer install`
- `vendor/bin/phpunit --coverage-clover=coverage.xml`
- Envoi vers Codecov

## 9. Dépannage et diagnostics

### SSH

- Vérifier la connectivité : `ping <host>`, `nc -zv <host> 22`
- Vérifier les logs SSH côté serveur (`/var/log/auth.log`)
- Vérifier que `ext-ssh2` est installé : `php -m | grep ssh2`
- Tester manuellement : `ssh -v user@host`

### PowerShell

- Inspecter `RemoteCommandOutput::getStderr()` pour les erreurs PowerShell
- Exécuter le script manuellement via SSH : `powershell -Command "..."`

### WinRM

- Vérifier `winrm quickconfig` sur la machine cible
- Vérifier les listeners : `winrm enumerate winrm/config/Listener`
- Vérifier le pare-feu (ports 5985 HTTP, 5986 HTTPS)

## 10. Contribuer et publier

- Fork et Pull Request sur https://github.com/LudovicBoudi/Cyonima.ops.php.lib
- Standards : PSR-12, typage strict (`declare(strict_types=1)`)
- Tests : PHPUnit, toutes les nouvelles fonctionnalités doivent être testées
- Analyse statique : `composer analyze` (PHPStan niveau 6, 0 erreurs)
- Versioning : SemVer, mettre à jour CHANGELOG.md, créer un tag Git

## 11. Annexes

### 11.1 Exceptions

| Exception | Déclenchée |
|-----------|------------|
| `Cyonima\Ops\Exception\ConnectionException` | Échec réseau, extension ssh2 manquante |
| `Cyonima\Ops\Exception\AuthenticationException` | Échec auth (mot de passe, clé, agent) |
| `Cyonima\Ops\Exception\ExecutionException` | Commande distante retourne exit ≠ 0 |

### 11.2 Fichiers importants

```
src/Cyonima/Ops/AbstractOps.php           # Classe de base SSH/SCP/SFTP
src/Cyonima/Ops/RemoteCommandOutput.php   # Valeur de retour
src/Cyonima/Ops/InputValidator.php        # Validation
src/Cyonima/Ops/Traits/UnixFileTrait.php  # Opérations fichier (Linux+BSD)
src/Cyonima/Ops/Linux/AbstractLinuxOps.php
src/Cyonima/Ops/Bsd/AbstractBsdOps.php
src/Cyonima/Ops/Windows/AbstractWindowsOps.php
src/Cyonima/Ops/Windows/WinRmClient.php
phpstan.neon                               # Config analyse statique
phpunit.xml                                # Config tests
```

### 11.3 Constantes

```php
AbstractOps::DEFAULT_SSH_PORT = 22
AbstractOps::DEFAULT_SSH_TIMEOUT = 30
```

---

*Fin du manuel détaillé.*
