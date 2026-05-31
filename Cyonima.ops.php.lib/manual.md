# Manuel complet — Cyonima OPS PHP Library

Dernière mise à jour : 31 mai 2026

Ce manuel est une documentation complète de la bibliothèque Cyonima OPS. Il fournit
des informations approfondies pour l'installation, la configuration, la compréhension
de l'architecture, l'utilisation des API, des exemples opérationnels, les bonnes
pratiques de sécurité, l'exécution des tests et l'intégration continue.

Table des matières
- 1. Vue d'ensemble
- 2. Installation et prérequis
- 3. Architecture et concepts clés
  - 3.1 `AbstractOps`
  - 3.2 `RemoteCommandOutput`
  - 3.3 `InputValidator` & échappement
  - 3.4 Logging (PSR-3)
- 4. Modules et API détaillées
  - 4.1 Linux (AbstractLinuxOps, UbuntuOps, dérivés)
  - 4.2 Windows (AbstractWindowsOps, WindowsOps, WinRmClient)
  - 4.3 macOS (AbstractMacOsOps, MacOsOps)
  - 4.4 Réseau / CiscoOps
  - 4.5 BSD (AbstractBsdOps, FreeBsdOps, OpenBsdOps)
  - 4.6 Azure (AbstractAzureOps, AzureOps)
  - 4.7 OpenStack (AbstractOpenStackOps, OpenStackOps)
  - 4.8 GCP (AbstractGcpOps, GcpOps)
  - 4.9 AWS (AbstractAwsOps, AwsOps)
- 5. Utilisation détaillée et exemples (avec snippets)
  - 5.1 Connexion SSH et exécution
  - 5.2 Transfert de fichiers (SCP)
  - 5.3 Windows: PowerShell over SSH & WinRM
  - 5.4 AD / IIS / SQL Server / Hyper-V exemples
  - 5.5 Linux Mint & Zorin usage
  - 5.6 FreeBSD / OpenBSD exemples
  - 5.7 Azure CLI examples
  - 5.8 OpenStack CLI examples
  - 5.9 GCP CLI examples
  - 5.10 AWS CLI examples
- 6. Sécurité, validation et échappement
- 7. Tests, couverture et CI (GitHub Actions)
- 8. Dépannage et diagnostics
- 9. Contribuer et publier
- 10. Annexes (exceptions, noms de fichiers importants)

---

1. Vue d'ensemble
------------------

Cyonima OPS est une bibliothèque PHP de haut niveau destinée à l'automatisation
d'administration d'infrastructures via SSH/SCP et WinRM. Elle encapsule les
opérations récurrentes (gestion des paquets, utilisateurs, services, firewall,
réseau, etc.) derrière des helpers typés et testables.

La bibliothèque privilégie :
- l'usage d'API claires et typées (PHP 8+),
- l'échappement systématique des arguments,
- la séparation des responsabilités (ops par OS / module),
- la compatibilité PSR-3 pour le logging.

2. Installation et prérequis
----------------------------

Requis (développement/CI) :
- PHP 8.0 ou supérieur
- Composer (pour les dépendances et l'exécution des tests)
- Extensions recommandées : `ssh2`, `curl`, `xdebug` (pour la couverture)

Installation :

```bash
composer require cyonima/ops-lib
```

Installer les dépendances de développement :

```bash
composer install --no-interaction
```

Configurer le CI : voir `.github/workflows/phpunit.yml` (exécute PHPUnit et envoie
la couverture à Codecov).

3. Architecture et concepts clés
--------------------------------

3.1 `AbstractOps`
- Point d'entrée de la configuration commune (host, port, credentials, proxy).
- Méthodes essentielles :
  - `setHost(string): self`, `setSshPort(int): self`
  - `setCredentials(string, string): self`
  - `setRsaAuthentication(string, string, string): self`
  - `openConnection(): void`, `closeConnection(): void`
  - `remoteExec(string): RemoteCommandOutput` — exécution distante.

La connexion SSH utilise `ext-ssh2` quand disponible. L'API essaie de ne pas
renvoyer de chaînes brutes : `RemoteCommandOutput` encapsule sortie et code.

3.2 `RemoteCommandOutput`
- Structure immuable contenant : `stdout`, `stderr`, `exitCode`.
- Méthodes utilitaires :
  - `isSuccessful(): bool` (exit 0)
  - `getStdout(): string`, `getStderr(): string`, `getExitCode(): int`
  - `throwIfFailed(): void` — lance `ExecutionException` si échec.

3.3 `InputValidator` & échappement
- `InputValidator` propose des vérifications (hôte, port, utilisateur,
  chemins, longueur des mots de passe, etc.).
- Fonctions d'échappement :
  - `escapeShellArgument($s)` — pour commandes shell Linux/macOS.
  - `escapePowerShellArgument($s)` — pour PowerShell (utilisée par Windows helpers).

Toujours valider avant d'appeler les helpers, surtout si des données utilisateurs
peuvent s'y retrouver.

3.4 Logging (PSR-3)
- `SimpleLogger` implémente PSR-3 et est injecté par défaut.
- Vous pouvez passer n'importe quel logger PSR-3 via `setLogger(Psr\\Log\\LoggerInterface $logger)`.

4. Modules et API détaillées
---------------------------

4.1 Linux

- `AbstractLinuxOps` expose :
  - gestion de fichiers : `readFile`, `writeFile`, `copyFileWithPrivilege`, `moveFile`, `removeFile`
  - gestion services : `startService`, `stopService`, `enableService`, `restartService`
  - packages : interface abstraite `installPackage`, implémentations `UbuntuOps` (apt),
    `RedhatOps` (yum/dnf), `SuseOps` (zypper), etc.
  - processus : `listProcesses`, `killProcess`.
  - réseau & firewall : `getNetworkInterfaces`, `addNetworkRoute`, `getFirewallStatus`, etc.

`UbuntuOps` utilise `apt` et fournit également des variantes `WithPrivilege` qui
utilisent `sudo` ou `executeWithSudo()` selon le cas.

4.2 Windows

- `AbstractWindowsOps` et `WindowsOps` (PowerShell over SSH) :
  - exécution PowerShell sécurisée via `toPowerShellCommand()` et `escapePowerShellArgument()`.
  - helpers : `readFile`, `writeFile` (utilise base64 pour transférer des contenus),
    `getProcesses`, `killProcessById`, `getEventLog`, `getNetworkInterfaces`, `addFirewallRule`.
- `WinRmClient` + `WindowsWinRmOps` : accès natif WinRM via SOAP/cURL (pour environnements non-SSH).
- Modules ajoutés : `IISOps`, `SQLServerOps`, `HyperVOps`, et squelettes `ExchangeOps`, `SharePointOps`.
- AD helpers : `joinDomain(domain, user, password, ou)`, `createAdUser`, `addAdUserToGroup`.

4.3 macOS

- `MacOsOps` reprend des helpers similaires à Linux mais adaptés : `brew` pour paquets,
  `brew services` pour services, `sysadminctl` pour utilisateurs.

4.4 Réseau / CiscoOps

- `CiscoOps` (dans `Network`) fournit des helpers orientés CLI réseau (configuration VLAN,
  assigning interfaces, routes). Ces helpers encapsulent les particularités
  des consoles réseau (delais, pagination, prompts).

4.5 BSD

- `AbstractBsdOps` expose des helpers génériques BSD pour :
  - gestion de fichiers : `readFile`, `writeFile`, `copyFileWithPrivilege`, `removeFile`
  - gestion de services et d'utilisateurs via les outils BSD natifs
  - gestion des paquets : `FreeBsdOps` utilise `pkg`, `OpenBsdOps` utilise `pkg_add`/`pkg_delete`
  - réseau et routes sur systèmes BSD
  - exécution de commandes shell et échappement POSIX
- `FreeBsdOps` et `OpenBsdOps` réutilisent les wrappers SSH/commande du noyau `AbstractOps`
  et adaptent la gestion de paquets aux conventions de chaque distribution.

4.6 Azure

- `AbstractAzureOps` fournit une couche d'exécution Azure CLI via SSH.
- Helpers inclus : `getAzureCliVersion`, `loginWithServicePrincipal`, `logout`,
  `listSubscriptions`, `setSubscription`, `listResourceGroups`,
  `createResourceGroup`, `deleteResourceGroup`, `listVirtualMachines`,
  `createVirtualMachine`, `startVirtualMachine`, `stopVirtualMachine`,
  `deleteVirtualMachine`.
- `AzureOps` est la classe concrète pour des usages directs.
- Ces helpers supposent que l'Azure CLI est installée et configurée sur la machine
  distante et accessible via la variable PATH.

4.7 OpenStack

- `AbstractOpenStackOps` fournit une couche d'exécution OpenStack CLI via SSH.
- Helpers inclus : `setOpenStackAuthentication`, `authenticate`, `getOpenStackVersion`,
  `listProjects`, `listFlavors`, `listImages`, `listNetworks`, `listServers`,
  `createServer`, `startServer`, `stopServer`, `deleteServer`.
- `OpenStackOps` est la classe concrète pour des usages directs.
- Ces helpers supposent que le client OpenStack est installé et disponible dans le
  PATH distant, et qu'un point d'authentification OpenStack est accessible.

4.8 GCP

- `AbstractGcpOps` fournit une couche d'exécution gcloud CLI via SSH.
- Helpers inclus : `getGcloudVersion`, `authenticateWithServiceAccountKey`,
  `listProjects`, `setProject`, `applyProject`, `listRegions`, `listZones`,
  `listInstances`, `createInstance`, `startInstance`, `stopInstance`,
  `deleteInstance`.
- `GcpOps` est la classe concrète pour des usages directs.
- Ces helpers supposent que le Google Cloud SDK est installé et accessible sur la
  machine distante.

4.9 AWS

- `AbstractAwsOps` fournit une couche d'exécution AWS CLI via SSH.
- Helpers inclus : `setProfile`, `setRegion`, `configureCredentials`,
  `getAwsCliVersion`, `listRegions`, `listZones`, `listInstances`,
  `createInstance`, `startInstance`, `stopInstance`, `terminateInstance`.
- `AwsOps` est la classe concrète pour des usages directs.
- Ces helpers supposent que l'AWS CLI est installé et accessible sur la
  machine distante.

5. Utilisation détaillée et exemples
-----------------------------------

5.1 Connexion SSH et exécution

```php
use Cyonima\Ops\Linux\UbuntuOps;

$ops = new UbuntuOps();
$ops->setHost('192.0.2.10')->setCredentials('ubuntu','secret')->setSshPort(22);
try {
    $ops->openConnection();
    $out = $ops->remoteExec('uname -a');
    echo $out->getStdout();
    $ops->closeConnection();
} catch (Exception $e) {
    // gérer ConnectionException / AuthenticationException / ExecutionException
}
```

5.2 Transfert de fichiers (SCP)

`AbstractOps` fournit `scpPut()` / `scpGet()` qui reposent sur `ssh2_scp_send`/
`ssh2_scp_recv` si l'extension est disponible, sinon recommandation d'utiliser
`rsync`/`sftp` externe.

Exemple :

```php
$ops->scpPut('/local/path/file.txt', '/remote/path/file.txt', '0644');
```

5.3 Windows: PowerShell over SSH & WinRM

- PowerShell over SSH : `WindowsOps::remoteExec($command)` attend une chaîne
  `toPowerShellCommand($psScript)` qui enrobe le script pour compatibilité.
- Pour fichiers binaires, `writeFile()` encode en base64 côté client et décodage
  côté cible pour éviter les problèmes d'encodage et d'échappement.
- WinRM : `WinRmClient` construit et envoie des requêtes SOAP; il expose des
  méthodes haut niveau via `WindowsWinRmOps`.

Exemple WinRM (squelette) :

```php
use Cyonima\Ops\Windows\WinRmClient;
$client = new WinRmClient('windows.example.local', 'Administrator', 'password');
$win = new WindowsWinRmOps($client);
$output = $win->getWindowsVersion();
```

5.4 AD / IIS / SQL Server / Hyper-V exemples

AD — joindre un domaine :

```php
$win->joinDomain('corp.example.local', 'corp\\admin', 'AdminP@ss');
```

IIS — installer et créer un site :

```php
use Cyonima\Ops\Windows\IISOps;
$iis = new IISOps();
$iis->installIIS();
$iis->createWebsite('Site', 'C:\\inetpub\\wwwroot\\site', 8080);
```

SQL Server — exécuter une requête :

```php
use Cyonima\Ops\Windows\SQLServerOps;
$sql = new SQLServerOps();
$sql->runQuery('localhost\\SQLEXPRESS', 'master', 'SELECT name FROM sys.databases');
```

Hyper-V — créer VM :

```php
use Cyonima\Ops\Windows\HyperVOps;
$hv = new HyperVOps();
$hv->createVM('vm1', 2048, 'C:\\vhd\\vm1.vhdx');
```

5.5 Linux Mint & Zorin

Ces distributions héritent d'Ubuntu — utilisez `LinuxMintOps` ou `ZorinOps` comme
`UbuntuOps`. Les différences sont principalement des paquets préinstallés et
quelques chemins, mais les commandes `apt` sont identiques.

5.6 FreeBSD / OpenBSD

Utilisez `FreeBsdOps` et `OpenBsdOps` pour gérer les hôtes BSD via SSH.
Ces forfaits réutilisent `AbstractBsdOps` et fournissent des commandes de gestion
de paquets adaptées :

- FreeBSD : `pkg install`, `pkg delete`, `pkg update`, `pkg upgrade`
- OpenBSD : `pkg_add`, `pkg_delete`, `pkg_info`

Exemple d'utilisation :

```php
use Cyonima\Ops\Bsd\FreeBsdOps;
use Cyonima\Ops\Bsd\OpenBsdOps;

$freebsd = new FreeBsdOps();
$freebsd->setHost('freebsd.example.local')->setCredentials('root', 'password')->setSshPort(22);
$freebsd->openConnection();
$freebsd->installPackage('nginx');
$freebsd->closeConnection();

$openbsd = new OpenBsdOps();
$openbsd->setHost('openbsd.example.local')->setCredentials('root', 'password')->setSshPort(22);
$openbsd->openConnection();
$openbsd->installPackage('nginx');
$openbsd->closeConnection();
```

5.7 Azure CLI examples

Pour utiliser Azure via SSH, installez l'Azure CLI sur le système distant et
utilisez `AzureOps` pour piloter des ressources Azure.

```php
use Cyonima\Ops\Azure\AzureOps;

$azure = new AzureOps();
$azure->setHost('azure-host.example.local')->setCredentials('ops','password')->setSshPort(22);
$azure->openConnection();
$azure->getAzureCliVersion();
$azure->loginWithServicePrincipal('TENANT_ID', 'CLIENT_ID', 'CLIENT_SECRET');
$azure->setSubscription('YOUR_SUBSCRIPTION_ID');
$azure->createResourceGroup('project-rg', 'westeurope');
$azure->listVirtualMachines('project-rg');
$azure->closeConnection();
```

5.8 OpenStack CLI examples

Pour utiliser OpenStack via SSH, installez le client `openstack` sur la machine
 distante et utilisez `OpenStackOps` pour piloter les ressources OpenStack.

```php
use Cyonima\Ops\OpenStack\OpenStackOps;

$openstack = new OpenStackOps();
$openstack->setHost('openstack-host.example.local')->setCredentials('ops','password')->setSshPort(22);
$openstack->openConnection();
$openstack->setOpenStackAuthentication(
    'https://openstack.example.local:5000/v3',
    'demo',
    'admin',
    'secret'
);
$openstack->authenticate();
$openstack->listServers();
$openstack->createServer('my-vm', 'Ubuntu20.04', 'm1.small', 'private-net', 'my-key');
$openstack->closeConnection();
```

5.9 GCP CLI examples

Pour utiliser GCP via SSH, installez le client `gcloud` sur la machine distante
et utilisez `GcpOps` pour piloter les ressources Google Cloud.

```php
use Cyonima\Ops\Gcp\GcpOps;

$gcp = new GcpOps();
$gcp->setHost('gcp-host.example.local')->setCredentials('ops','password')->setSshPort(22);
$gcp->openConnection();
$gcp->authenticateWithServiceAccountKey('/tmp/service-account.json');
$gcp->setProject('my-gcp-project');
$gcp->applyProject();
$gcp->listInstances('europe-west1-b');
$gcp->createInstance('my-instance', 'europe-west1-b', 'e2-medium', 'ubuntu-2004-lts', 'ubuntu-os-cloud', 'default');
$gcp->closeConnection();
```

5.10 AWS CLI examples

Pour utiliser AWS via SSH, installez le client `aws` sur la machine distante
et utilisez `AwsOps` pour piloter les ressources AWS.

```php
use Cyonima\Ops\Aws\AwsOps;

$aws = new AwsOps();
$aws->setHost('aws-host.example.local')->setCredentials('ops','password')->setSshPort(22);
$aws->setProfile('default');
$aws->setRegion('eu-west-1');
$aws->openConnection();
$aws->configureCredentials('AKIAEXAMPLE', 'secret-key', 'eu-west-1');
$aws->listInstances('eu-west-1a');
$aws->createInstance('my-instance', 'ami-12345678', 't3.micro', 'subnet-01234567', 'my-key', 'sg-01234567');
$aws->closeConnection();
```

6. Sécurité, validation et échappement
-------------------------------------

- Toujours utiliser `InputValidator` pour valider : hôtes, ports, chemins, noms
  d'utilisateur, et commandes acceptées.
- Pour PowerShell, utiliser `escapePowerShellArgument()` ; pour shell POSIX,
  `escapeShellArgument()`.
- Préférez RSA keys pour SSH et restreignez les permissions (`chmod 600` sur
  les clés privées).
- Ne jamais conserver de mots de passe en clair dans le dépôt ; préférez les
  variables d'environnement ou un gestionnaire de secrets (Vault, AWS Secrets).
- Pour opérations critiques, exiger une approbation humaine ou exécuter sur
  environnement de staging avant production.

7. Tests, couverture et CI
-------------------------

- Tests : PHPUnit. Les tests unitaires sont principalement des tests de
  génération de commandes et des wrappers (`tests/*.php`).
- Workflow CI : `.github/workflows/phpunit.yml` exécute les tests sur PHP 8.0/8.1/8.2,
  génère `coverage.xml` et l'envoie à Codecov.

Conseils pour CI local :

```bash
composer install
vendor/bin/phpunit --configuration phpunit.xml --coverage-clover=coverage.xml
```

8. Dépannage et diagnostics
---------------------------

- Problèmes de connexion SSH :
  - Vérifier reachability (`ping`, `nc -zv host port`), logs SSH côté serveur,
    et que `ext-ssh2` est installé si vous utilisez les bindings PHP.
- Erreurs PowerShell : inspecter `RemoteCommandOutput::getStderr()` et
  exécuter manuellement le script via OpenSSH sur la machine cible.
- WinRM : vérifier `winrm quickconfig` et listeners (5985/5986), certificats
  et pare-feu.

9. Contribuer et publier
------------------------

- Forkez et ouvrez une Pull Request. Respectez PSR-12 et fournissez des tests.
- Versioning : utilisez SemVer. Mettez à jour `CHANGELOG.md` et créez une tag
  Git pour les releases.

10. Annexes
-----------

Exceptions principales :
- `ConnectionException` — échec réseau/SSH
- `AuthenticationException` — échec d'authentification
- `ExecutionException` — commande distante retournant un code non nul

Fichiers importants :
- `src/Cyonima/Ops/AbstractOps.php`
- `src/Cyonima/Ops/RemoteCommandOutput.php`
- `src/Cyonima/Ops/InputValidator.php`
- `src/Cyonima/Ops/Windows/AbstractWindowsOps.php`
- `src/Cyonima/Ops/Linux/UbuntuOps.php`

Contact
-------

Ouvrez une issue sur : https://github.com/LudovicBoudi/Cyonima.ops.php.lib

---

Fin du manuel détaillé.

---

Vue d'ensemble
---------------

Cyonima OPS est une bibliothèque PHP moderne (PHP 8.0+) destinée à automatiser des tâches d'administration système et réseau via SSH, SCP et WinRM. Elle fournit des abstractions pour exécuter des commandes distantes, transférer des fichiers de manière sécurisée et exposer des helpers par-plateforme (Linux distributions, Windows, macOS) pour gérer services, utilisateurs, paquets, firewall, réseau et plus.

Exigences et installation
-------------------------

Requis côté développeur/CI :
- PHP 8.0 ou supérieur
- Composer
- Extensions PHP recommandées : `ssh2`, `curl`, `xdebug` (pour la couverture)

Installation via Composer :

```bash
composer require cyonima/ops-lib
```

Configuration minimale pour exécuter les tests en local :

```bash
composer install --dev
vendor/bin/phpunit
```

Configuration et authentification
---------------------------------

La classe de base `AbstractOps` fournit la configuration commune : hôte, port SSH, utilisateur et méthode d'authentification (mot de passe ou clé RSA). Exemple :

```php
$ops->setHost('192.0.2.10')
    ->setCredentials('user', 'password')
    ->setSshPort(22);

$ops->openConnection();
```

Pour l'authentification par clé RSA :

```php
$ops->setRsaAuthentication('user', '/path/id_rsa', '/path/id_rsa.pub');
```

Concepts clés
-------------

AbstractOps et connexion SSH

- `openConnection()` / `closeConnection()` : ouverture/fermeture de la session SSH.
- `remoteExec(string $command): RemoteCommandOutput` : exécute une commande distante et retourne un objet `RemoteCommandOutput` contenant stdout, stderr et code de sortie.

RemoteCommandOutput

Objet valeur retourné par les exécutions distantes. Méthodes utiles :
- `getStdout()`, `getStderr()`, `getExitCode()`
- `isSuccessful(): bool` — vrai si `exitCode === 0`
- `throwIfFailed()` — lance une exception `ExecutionException` si non réussi.

InputValidator

Utilisé pour valider les hôtes, les ports, chemins de fichiers et commandes pour réduire les vecteurs d'injection.

SimpleLogger

Un logger PSR-3 minimal fourni par défaut. Vous pouvez injecter votre propre implémentation PSR-3.

Modules principaux et API
------------------------

Linux

- `AbstractLinuxOps` : helpers transverses pour fichiers, services (`systemctl`), processus, réseau, firewall, SELinux/AppArmor.
- Distributions concrètes : `UbuntuOps`, `DebianOps`, `SuseOps`, `FedoraOps`, `RedhatOps`, `CentosOps`, `RockyOps`.
- Dérivés Ubuntu : `LinuxMintOps`, `ZorinOps` (réutilisent les commandes `apt`).

Exemples de méthodes :
- `readFile($path)`, `writeFile($path, $content)`, `copyFile()`, `moveFile()`, `removeFile()`
- `installPackage($pkg)`, `uninstallPackage($pkg)`, `upgradePackages()`
- `startService($name)`, `stopService($name)`, `enableService($name)`

Windows

- `AbstractWindowsOps` expose des helpers PowerShell over SSH et des adaptateurs WinRM.
- `WindowsOps` : classe concrète PowerShell over SSH.
- `WinRmClient` + `WindowsWinRmOps` : accès WinRM (HTTP/HTTPS) si SSH non disponible.
- Modules avancés ajoutés : `IISOps`, `SQLServerOps`, `HyperVOps`, plus des squelettes `ExchangeOps` et `SharePointOps`.
- AD helpers intégrés dans `AbstractWindowsOps` : `joinDomain`, `createAdUser`, `addAdUserToGroup`, `createOrganizationalUnit`.

Exemples :

```php
use Cyonima\Ops\Windows\IISOps;
$iis = new IISOps();
$iis->setHost('win.example')->setCredentials('Administrator','pass')->openConnection();
$iis->installIIS();
$iis->createWebsite('MySite', 'C:\\inetpub\\wwwroot\\mysite', 8080);
$iis->closeConnection();
```

macOS

- `AbstractMacOsOps` / `MacOsOps` : helpers Homebrew (`brew`), `sysadminctl` pour comptes, et version reporting.

Réseau & Cisco

- `Cyonima\Ops\Network\CiscoOps` : helpers pour configurer VLAN, interfaces, routes et ACL sur équipement Cisco (via SSH sessions spécifiques aux consoles réseau).

Exemples détaillés
------------------

Opérations fichiers (Linux)

```php
$linux->openConnection();
$out = $linux->readFile('/var/log/syslog');
echo $out->getStdout();
$linux->writeFile('/tmp/message.txt', "Hello\n");
$linux->closeConnection();
```

Opérations fichiers (Windows)

```php
use Cyonima\Ops\Windows\WindowsOps;
$win = new WindowsOps();
$win->setHost('win')->setCredentials('Administrator','pass')->openConnection();
$content = $win->readFile('C:\\temp\\example.log');
echo $content->getStdout();
$win->writeFile('C:\\temp\\out.txt', "Hello\n");
$win->closeConnection();
```

Gestion de paquets

- Linux (Ubuntu et dérivés) : `installPackage`, `upgradePackages` (utilise `apt`).
- Windows : `installPackage` via `winget` ou `choco` (command builder détecte la disponibilité).
- macOS : `brew install`, `brew upgrade`.

Services et processus

- `startService`, `stopService`, `restartService` (Linux/macOS via `systemctl` / `brew services`)
- `getProcesses`, `killProcessById`, `killProcessByName` (Windows helpers retournent JSON compact pour parsing)

Active Directory, IIS, SQL Server, Hyper-V (Windows)

- AD: `joinDomain($domain, $user, $pass, $ou)`, `createAdUser`, `addAdUserToGroup`, `createOrganizationalUnit`.
- IIS: `IISOps::installIIS()`, `createWebsite()`, `startWebsite()`.
- SQL: `SQLServerOps::runQuery($instance, $db, $query)` utilise `Invoke-Sqlcmd` si présent.
- Hyper-V: `HyperVOps::createVM`, `startVM`, `stopVM`, `removeVM`.

Tests et CI
------------

Le projet inclut des tests PHPUnit (fichiers `tests/*.php`). Un workflow GitHub Actions est configuré dans `.github/workflows/phpunit.yml` pour exécuter les tests sur PHP 8.0/8.1/8.2, générer `coverage.xml` et envoyer la couverture à Codecov.

Exécution locale :

```bash
composer install
vendor/bin/phpunit --configuration phpunit.xml
```

Pour exécuter la couverture localement :

```bash
vendor/bin/phpunit --configuration phpunit.xml --coverage-clover=coverage.xml
bash <(curl -s https://codecov.io/bash) -f coverage.xml
```

Sécurité et bonnes pratiques
----------------------------

- Valider et échapper toutes les entrées utilisateur : utilisez `InputValidator` et les helpers `escapeShellArgument` / `escapePowerShellArgument` avant toute interpolation.
- Préférer les clés RSA pour SSH plutôt que des mots de passe, et limiter les privilèges.
- Pour les opérations sensibles (AD, création d'OU, modifications de firewall), tester d'abord sur environnement de staging.
- Ne stockez pas de secrets en clair dans le code ; utilisez des variables d'environnement ou un vault.

Dépannage
---------

- Connexions SSH échouent : vérifier `ssh2` extension, network reachability, pare-feu et port SSH.
- WinRM échoue : vérifier écouteurs WinRM (5985/5986), certificats et permissions.
- Commandes retournent du texte incohérent : inspecter `getStdout()` et `getStderr()` via `RemoteCommandOutput` et exécuter manuellement la commande sur la cible pour debug.

Contribution et roadmap
-----------------------

Contributions bienvenues : ouvrez une Pull Request en respectant PSR-12 et en incluant des tests pour toute nouvelle fonctionnalité.

Fonctionnalités prévues / roadmap courte :
- Améliorer la couverture de tests d'injection/validation PowerShell
- Modules avancés Windows : Exchange/SharePoint approfondis
- Modules cloud/virtualisation supplémentaires (VMware, OpenStack)

Annexes — Références rapides
---------------------------

- Fichiers d'intérêt :
  - `src/Cyonima/Ops/AbstractOps.php`
  - `src/Cyonima/Ops/RemoteCommandOutput.php`
  - `src/Cyonima/Ops/InputValidator.php`
  - `src/Cyonima/Ops/Windows/AbstractWindowsOps.php`
  - `src/Cyonima/Ops/Linux/UbuntuOps.php`

API approfondie par classe
---------------------------

- **Fichier :** `src/Cyonima/Ops/Windows/SQLServerOps.php`
  - **But :** Exécuter une requête T-SQL sur une instance SQL Server distante.
  - **Méthode :** `runQuery(string $serverInstance, string $database, string $query): RemoteCommandOutput` — utilise `Invoke-Sqlcmd` et encode la requête en base64 pour éviter les problèmes d'échappement.

- **Fichier :** `src/Cyonima/Ops/Windows/HyperVOps.php`
  - **But :** Gestion de machines virtuelles Hyper-V via PowerShell.
  - **Méthodes :**
    - `createVM(string $name, int $memoryMB, string $vhdPath): RemoteCommandOutput`
    - `startVM(string $name): RemoteCommandOutput`
    - `stopVM(string $name): RemoteCommandOutput`
    - `removeVM(string $name): RemoteCommandOutput`

- **Fichier :** `src/Cyonima/Ops/Windows/WinRmClient.php`
  - **But :** Client WinRM minimal SOAP/HTTP(S) pour exécuter des commandes PowerShell sur une machine Windows distante.
  - **Constructeur :** `__construct(string $host, string $username, string $password, int $port = 5985, bool $useHttps = false, ?LoggerInterface $logger = null)`.
  - **Méthodes clés :**
    - `executePowerShell(string $script): RemoteCommandOutput` — exécute un script PowerShell.
    - `executeCommand(string $command): RemoteCommandOutput` — exécute une commande shell via WinRM.
  - **Méthodes internes importantes :**
    - `createShell(): string`
    - `sendCommand(string $shellId, string $command): string`
    - `receiveOutput(string $shellId, string $commandId): RemoteCommandOutput`
    - `deleteShell(string $shellId): void`

- **Fichier :** `src/Cyonima/Ops/Windows/WindowsWinRmOps.php`
  - **But :** Adaptateur WinRM de haut niveau qui expose des helpers équivalents à `AbstractWindowsOps` sur une connexion WinRM.
  - **Méthodes clés :**
    - `getWindowsVersion(): RemoteCommandOutput`
    - `getService(string $serviceName): RemoteCommandOutput`
    - `manageService(string $serviceName, string $action): RemoteCommandOutput`
    - `addUser(string $username, string $password, ?string $group = null): RemoteCommandOutput`
    - `deleteUser(string $username): RemoteCommandOutput`
    - `changePassword(string $username, string $newPassword): RemoteCommandOutput`
    - `installPackage(string $packageId): RemoteCommandOutput`
    - `uninstallPackage(string $packageId): RemoteCommandOutput`
    - `upgradePackages(): RemoteCommandOutput`
  - **Notes :** conserve les mêmes concepts de gestion de services, d'utilisateurs et de paquets que `AbstractWindowsOps`, mais sur WinRM.

- **Fichier :** `src/Cyonima/Ops/Network/CiscoOps.php`
  - **But :** Automatiser la configuration de commutateurs et routeurs Cisco via CLI réseau.
  - **Méthodes clés :**
    - `configureHostBase(string $hostname, string $location, string $time): string`
    - `addUser(string $username, string $password): string`
    - `configureVlan(string $vlanNumber, string $vlanName, string $vlanInterface): string`
    - `addInterfaceRangeToVlan(string $vlanNumber, string $interface, string $interfaceRange): string`
    - `configureInterface(string $interface, string $type, string $description, string $speed, string $duplexMode): string`
    - `setVlanIpAddress(string $vlanNumber, string $description, string $ipAddress, string $netmask): string`
    - `addRoute(string $networkIp, string $netmask, string $gateway, string $metric): string`
    - `addDefaultGateway(string $gateway): string`
    - `addAclBasic(string $aclNumber, string $action, string $ip, string $mask): string`
    - `configureNetflow(string $collectorIp, string $sourceInterface): string`
    - `addInterfaceToNetflowMonitoring(string $interface): string`

Exemples d'utilisation des API
-----------------------------

### SQLServerOps

```php
use Cyonima\Ops\Windows\SQLServerOps;

$sql = new SQLServerOps();
$sql->setHost('sqlserver.example.local')->setCredentials('Administrator', 'P@ssw0rd')->openConnection();
$result = $sql->runQuery('localhost\\SQLEXPRESS', 'master', 'SELECT name FROM sys.databases');
echo $result->getStdout();
$sql->closeConnection();
```

### HyperVOps

```php
use Cyonima\Ops\Windows\HyperVOps;

$hv = new HyperVOps();
$hv->setHost('hyperv.example.local')->setCredentials('Administrator', 'P@ssw0rd')->openConnection();
$hv->createVM('TestVM', 4096, 'C:\\vm\\TestVM.vhdx');
$hv->startVM('TestVM');
$hv->closeConnection();
```

### WinRmClient

```php
use Cyonima\Ops\Windows\WinRmClient;

$client = new WinRmClient('winrm.example.local', 'Administrator', 'P@ssw0rd', 5985, false);
$output = $client->executePowerShell('Get-Process | Select-Object -First 5 | ConvertTo-Json -Compress');
echo $output->getStdout();
```

### WindowsWinRmOps

```php
use Cyonima\Ops\Windows\WinRmClient;
use Cyonima\Ops\Windows\WindowsWinRmOps;

$client = new WinRmClient('winrm.example.local', 'Administrator', 'P@ssw0rd');
$winOps = new WindowsWinRmOps($client);
$out = $winOps->getWindowsVersion();
echo $out->getStdout();
```

### CiscoOps

```php
use Cyonima\Ops\Network\CiscoOps;

$cisco = new CiscoOps();
$cisco->setHost('10.0.0.1')->setCredentials('admin', 'cisco123')->openConnection();
echo $cisco->configureHostBase('SW1', 'Datacenter', '12:00:00');
$cisco->addRoute('10.10.0.0', '255.255.255.0', '10.0.0.254', '1');
$cisco->closeConnection();
```

Contact / Support
-----------------

Pour toute question ou rapport de bug, ouvrez une issue dans le dépôt : https://github.com/LudovicBoudi/Cyonima.ops.php.lib

---

Fin du manuel.
