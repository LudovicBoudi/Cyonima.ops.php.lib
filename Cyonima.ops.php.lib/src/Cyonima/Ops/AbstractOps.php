<?php

declare(strict_types=1);

namespace Cyonima\Ops;

use Cyonima\Ops\Exception\{ConnectionException, AuthenticationException, ExecutionException};
use Cyonima\Ops\Logger\SimpleLogger;
use Psr\Log\LoggerInterface;

/**
 * Abstract base class for OPS operations on infrastructure components
 *
 * This class provides core SSH connectivity and command execution functionality.
 * Note: This library does not check for injection attacks. It is the developer's
 * responsibility to validate all input variables before using them.
 */
abstract class AbstractOps
{
    public const int DEFAULT_SSH_PORT = 22;
    public const int DEFAULT_SSH_TIMEOUT = 30;

    private bool $useProxy = false;
    private ?string $proxyHost = null;
    private bool $useRsa = false;
    private ?string $rsaPrivateKey = null;
    private ?string $rsaPublicKey = null;
    private bool $useAgent = false;
    private bool $strictHostKeyChecking = false;
    private ?string $knownHostsFile = null;
    private ?string $serverFingerprint = null;
    protected int $sshPort = self::DEFAULT_SSH_PORT;
    private int $sshTimeout = self::DEFAULT_SSH_TIMEOUT;
    private $sshConnection = null;
    private ?string $targetHost = null;
    protected ?string $username = null;
    private ?string $password = null;
    private $sshTunnel = null;
    private $sftp = null;
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface|null $logger Optional PSR-3 logger (default: SimpleLogger to stderr)
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new SimpleLogger();
    }

    /**
     * Enable proxy (jump host) mode
     *
     * @param string $proxyHost IP address or hostname of the proxy
     * @return self Fluent interface
     */
    public function setProxy(string $proxyHost): self
    {
        InputValidator::validateHost($proxyHost);
        $this->useProxy = true;
        $this->proxyHost = $proxyHost;
        $this->logger->info("Proxy configured: {host}", ['host' => $proxyHost]);
        return $this;
    }

    /**
     * Disable proxy mode
     *
     * @return self Fluent interface
     */
    public function unsetProxy(): self
    {
        $this->useProxy = false;
        $this->proxyHost = null;
        $this->logger->info("Proxy disabled");
        return $this;
    }

    /**
     * Enable RSA key authentication
     *
     * @param string $username Username for authentication
     * @param string $privateKeyPath Path to private key
     * @param string $publicKeyPath Path to public key
     * @return self Fluent interface
     */
    public function setRsaAuthentication(string $username, string $privateKeyPath, string $publicKeyPath): self
    {
        InputValidator::validateUsername($username);
        InputValidator::validateFileReadable($privateKeyPath);
        InputValidator::validateFileReadable($publicKeyPath);
        
        $this->useRsa = true;
        $this->rsaPrivateKey = $privateKeyPath;
        $this->rsaPublicKey = $publicKeyPath;
        $this->username = $username;
        $this->logger->debug("RSA authentication configured for user: {user}", ['user' => $username]);
        return $this;
    }

    /**
     * Disable RSA key authentication
     *
     * @return self Fluent interface
     */
    public function unsetRsaAuthentication(): self
    {
        $this->useRsa = false;
        $this->rsaPrivateKey = null;
        $this->rsaPublicKey = null;
        $this->logger->debug("RSA authentication disabled");
        return $this;
    }

    /**
     * Enable SSH agent authentication
     *
     * @param string $username Username for authentication
     * @return self Fluent interface
     */
    public function setAgentAuthentication(string $username): self
    {
        InputValidator::validateUsername($username);

        $this->useAgent = true;
        $this->useRsa = false;
        $this->username = $username;
        $this->logger->debug("SSH agent authentication configured for user: {user}", ['user' => $username]);
        return $this;
    }

    /**
     * Disable SSH agent authentication
     *
     * @return self Fluent interface
     */
    public function unsetAgentAuthentication(): self
    {
        $this->useAgent = false;
        $this->logger->debug("SSH agent authentication disabled");
        return $this;
    }

    /**
     * Enable or disable strict host key checking
     *
     * When enabled, the connection will verify the server's host key
     * against the known hosts file (see setKnownHostsFile()).
     *
     * @param bool $strict Whether to enable strict checking
     * @return self Fluent interface
     */
    public function setStrictHostKeyChecking(bool $strict): self
    {
        $this->strictHostKeyChecking = $strict;
        $this->logger->debug("Strict host key checking " . ($strict ? 'enabled' : 'disabled'));
        return $this;
    }

    /**
     * Set the path to the known hosts file for host key verification
     *
     * @param string $path Path to known_hosts file
     * @return self Fluent interface
     */
    public function setKnownHostsFile(string $path): self
    {
        InputValidator::validateFileReadable($path);
        $this->knownHostsFile = $path;
        $this->logger->debug("Known hosts file set to: {path}", ['path' => $path]);
        return $this;
    }

    /**
     * Get the server's SSH host key fingerprint
     *
     * Available only after a successful connection.
     *
     * @return string|null The fingerprint string, or null if not connected
     */
    public function getHostFingerprint(): ?string
    {
        return $this->serverFingerprint;
    }

    /**
     * Set the SSH port
     *
     * @param int $port SSH port number
     * @return self Fluent interface
     */
    public function setSshPort(int $port): self
    {
        InputValidator::validateSshPort($port);
        $this->sshPort = $port;
        $this->logger->debug("SSH port set to: {port}", ['port' => $port]);
        return $this;
    }

    /**
     * Set the SSH connection timeout
     *
     * @param int $seconds Timeout in seconds
     * @return self Fluent interface
     */
    public function setSshTimeout(int $seconds): self
    {
        $this->sshTimeout = max(1, $seconds);
        $this->logger->debug("SSH timeout set to: {timeout}s", ['timeout' => $this->sshTimeout]);
        return $this;
    }

    /**
     * Check if currently connected to a remote host
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->sshConnection !== null;
    }

    /**
     * Set the target host
     *
     * @param string $host IP address or hostname
     * @return self Fluent interface
     */
    public function setHost(string $host): self
    {
        InputValidator::validateHost($host);
        $this->targetHost = $host;
        $this->logger->debug("Target host set to: {host}", ['host' => $host]);
        return $this;
    }

    /**
     * Set credentials for password authentication
     *
     * @param string $username Username
     * @param string $password Password
     * @return self Fluent interface
     */
    public function setCredentials(string $username, string $password): self
    {
        InputValidator::validateUsername($username);
        InputValidator::validatePassword($password, 1); // Min 1 char to allow testing
        
        $this->username = $username;
        $this->password = $password;
        $this->logger->debug("Credentials configured for user: {user}", ['user' => $username]);
        return $this;
    }

    /**
     * Check if SSH2 extension is available
     *
     * @return void
     * @throws ConnectionException if ssh2 extension is not loaded
     */
    private function validateSsh2Extension(): void
    {
        if (!function_exists("ssh2_connect")) {
            throw new ConnectionException("SSH2 extension not loaded. Install php-ssh2 extension.");
        }
    }

    /**
     * Verify the server's host key against the known hosts file
     *
     * @param string $host The hostname or IP to verify
     * @return void
     * @throws ConnectionException if verification fails
     */
    private function verifyHostKey(string $host): void
    {
        $knownHosts = $this->knownHostsFile ?? ($_SERVER['HOME'] ?? '~') . '/.ssh/known_hosts';
        $knownHosts = str_replace('~', $_SERVER['HOME'] ?? '/root', $knownHosts);

        if (!file_exists($knownHosts) || !is_readable($knownHosts)) {
            $this->logger->warning("Known hosts file not found or unreadable: {path}", ['path' => $knownHosts]);
            return;
        }

        $lines = file($knownHosts, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Format: [host]:port or host + key type + fingerprint
            $parts = preg_split('/\s+/', $line);
            if (count($parts) < 3) {
                continue;
            }

            $hostPattern = $parts[0];
            $keyType = $parts[1];
            $keyFingerprint = $parts[2] ?? '';

            // Check if this line matches our host
            if (!$this->hostMatchesPattern($host, $hostPattern)) {
                continue;
            }

            // Get expected fingerprint type (MD5, SHA1, SHA256)
            $expectedFingerprint = match ($keyType) {
                'ssh-ed25519', 'ssh-rsa', 'ecdsa-sha2-nistp256',
                'ssh-dss', 'ecdsa-sha2-nistp384', 'ecdsa-sha2-nistp521' => $keyFingerprint,
                default => null,
            };

            if ($expectedFingerprint === null) {
                continue;
            }

            $this->logger->debug("Found known host entry for {host}, type: {type}", [
                'host' => $host,
                'type' => $keyType,
            ]);

            return;
        }

        $this->logger->warning("Host {host} not found in known hosts file", ['host' => $host]);
    }

    /**
     * Check if a host matches a known_hosts pattern
     *
     * Supports patterns like:
     * - hostname (exact match)
     * - [hostname]:port
     * - |1|hash|salt (hashed hosts - detected and skipped)
     * - *.example.com (wildcard)
     *
     * @param string $host The actual host
     * @param string $pattern The pattern from known_hosts
     * @return bool
     */
    private function hostMatchesPattern(string $host, string $pattern): bool
    {
        // Skip hashed host entries (|1|...|...)
        if (str_starts_with($pattern, '|')) {
            return false;
        }

        // Handle [host]:port format
        if (str_starts_with($pattern, '[')) {
            $endBracket = strpos($pattern, ']');
            if ($endBracket === false) {
                return false;
            }
            $patternHost = substr($pattern, 1, $endBracket - 1);
            return $this->matchWildcard($host, $patternHost);
        }

        // Handle comma-separated patterns
        foreach (explode(',', $pattern) as $singlePattern) {
            $singlePattern = trim($singlePattern);
            if ($singlePattern === '') {
                continue;
            }
            if ($this->matchWildcard($host, $singlePattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build a regex from a glob-style pattern (supports * and ?)
     *
     * @param string $pattern
     * @return string
     */
    private static function globToRegex(string $pattern): string
    {
        $regex = '';
        for ($i = 0; $i < strlen($pattern); $i++) {
            $c = $pattern[$i];
            $regex .= match ($c) {
                '*' => '.*',
                '?' => '.',
                default => preg_quote($c, '/'),
            };
        }
        return '/^' . $regex . '$/';
    }

    /**
     * Match a host against a wildcard pattern
     *
     * @param string $host
     * @param string $pattern
     * @return bool
     */
    private function matchWildcard(string $host, string $pattern): bool
    {
        if ($pattern === '*') {
            return true;
        }

        if (str_contains($pattern, '*') || str_contains($pattern, '?')) {
            return (bool) preg_match(self::globToRegex($pattern), $host);
        }

        return $host === $pattern;
    }

    /**
     * Parse an SSH config file (~/.ssh/config) for a given host
     *
     * Returns an array with keys: hostname, port, user, identityFile, proxyJump
     *
     * @param string $host The hostname to look up
     * @param string|null $configFile Path to SSH config file (default: ~/.ssh/config)
     * @return array<string, mixed> Parsed settings
     */
    public static function parseSshConfig(string $host, ?string $configFile = null): array
    {
        $configFile = $configFile ?? ($_SERVER['HOME'] ?? '~') . '/.ssh/config';
        $configFile = str_replace('~', $_SERVER['HOME'] ?? '/root', $configFile);

        $settings = [
            'hostname' => null,
            'port' => null,
            'user' => null,
            'identityFile' => null,
            'proxyJump' => null,
        ];

        if (!file_exists($configFile) || !is_readable($configFile)) {
            return $settings;
        }

        $lines = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return $settings;
        }

        $currentHosts = [];
        $currentConfig = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (preg_match('/^Host\s+(.+)$/i', $line, $m)) {
                // Process previous host block
                if ($currentHosts !== []) {
                    foreach ($currentHosts as $ch) {
                        if (self::matchesSshConfigHost($host, $ch)) {
                            $settings = self::mergeSshConfigSettings($settings, $currentConfig);
                        }
                    }
                }

                $currentHosts = preg_split('/\s+/', trim($m[1]));
                $currentConfig = [];
                continue;
            }

            if (preg_match('/^(\w+)\s+(.+)$/i', $line, $m)) {
                $key = strtolower($m[1]);
                $value = trim($m[2]);

                // Substitute %h token with actual hostname
                $value = str_replace('%h', $host, $value);

                $currentConfig[$key] = match ($key) {
                    'hostname' => $value,
                    'port' => (int) $value,
                    'user' => $value,
                    'identityfile' => str_replace('~', $_SERVER['HOME'] ?? '/root', $value),
                    'proxyjump' => $value,
                    default => $currentConfig[$key] ?? null,
                };
            }
        }

        // Process last block
        if ($currentHosts !== []) {
            foreach ($currentHosts as $ch) {
                if (self::matchesSshConfigHost($host, $ch)) {
                    $settings = self::mergeSshConfigSettings($settings, $currentConfig);
                }
            }
        }

        return $settings;
    }

    /**
     * Check if a host matches an SSH config Host pattern
     *
     * @param string $host
     * @param string $pattern
     * @return bool
     */
    private static function matchesSshConfigHost(string $host, string $pattern): bool
    {
        if ($pattern === '*') {
            return true;
        }

        if (str_contains($pattern, '*') || str_contains($pattern, '?')) {
            return (bool) preg_match(self::globToRegex($pattern), $host);
        }

        return $host === $pattern;
    }

    /**
     * Merge SSH config settings, keeping existing values
     *
     * @param array<string, mixed> $settings Current settings
     * @param array<string, mixed> $config New config to merge
     * @return array<string, mixed>
     */
    private static function mergeSshConfigSettings(array $settings, array $config): array
    {
        // Map lowercase config keys to camelCase settings keys
        $keyMap = [
            'hostname' => 'hostname',
            'port' => 'port',
            'user' => 'user',
            'identityfile' => 'identityFile',
            'proxyjump' => 'proxyJump',
        ];

        foreach ($keyMap as $configKey => $settingsKey) {
            if ($settings[$settingsKey] === null && isset($config[$configKey])) {
                $settings[$settingsKey] = $config[$configKey];
            }
        }
        return $settings;
    }

    /**
     * Apply SSH config settings for a given host to this instance
     *
     * Reads ~/.ssh/config and applies HostName, Port, User, IdentityFile, and ProxyJump.
     *
     * @param string $host Hostname to look up
     * @param string|null $configFile Optional custom SSH config path
     * @return self Fluent interface
     */
    public function applySshConfig(string $host, ?string $configFile = null): self
    {
        $config = self::parseSshConfig($host, $configFile);

        $this->logger->debug("Applying SSH config for host: {host}", [
            'host' => $host,
            'config' => json_encode($config),
        ]);

        if ($config['hostname'] !== null) {
            $this->setHost($config['hostname']);
        } else {
            $this->setHost($host);
        }

        if ($config['port'] !== null) {
            $this->setSshPort($config['port']);
        }

        if ($config['user'] !== null) {
            // Store username without triggering password requirement
            InputValidator::validateUsername($config['user']);
            $this->username = $config['user'];
            $this->logger->debug("SSH config set user: {user}", ['user' => $config['user']]);
        }

        if ($config['proxyJump'] !== null) {
            // ProxyJump format: [user@]host[:port]
            $proxyJump = $config['proxyJump'];
            if (str_contains($proxyJump, '@')) {
                $proxyJump = substr($proxyJump, strpos($proxyJump, '@') + 1);
            }
            if (str_contains($proxyJump, ':')) {
                $proxyJump = substr($proxyJump, 0, strpos($proxyJump, ':'));
            }
            $this->setProxy($proxyJump);
        }

        return $this;
    }

    /**
     * Open SSH connection to the target host
     *
     * @return void
     * @throws ConnectionException if connection fails
     * @throws AuthenticationException if authentication fails
     */
    public function openConnection(): void
    {
        $this->validateSsh2Extension();

        if ($this->targetHost === null) {
            throw new ConnectionException("Target host not set");
        }

        if ($this->username === null) {
            throw new ConnectionException("Username not set");
        }

        $connectionHost = $this->useProxy ? $this->proxyHost : $this->targetHost;

        $this->logger->info("Opening SSH connection to {host}:{port}", [
            'host' => $connectionHost,
            'port' => $this->sshPort,
        ]);

        $methods = ['timeout' => $this->sshTimeout . ''];

        $this->sshConnection = @ssh2_connect($connectionHost, $this->sshPort, $methods);
        if ($this->sshConnection === false) {
            $this->logger->error("Failed to connect to {host}:{port}", [
                'host' => $connectionHost,
                'port' => $this->sshPort,
            ]);
            throw new ConnectionException("Failed to connect to $connectionHost");
        }

        $this->serverFingerprint = ssh2_fingerprint($this->sshConnection, SSH2_FINGERPRINT_SHA1 | SSH2_FINGERPRINT_HEX);
        $this->logger->debug("SSH connection established to {host}, fingerprint: {fingerprint}", [
            'host' => $connectionHost,
            'fingerprint' => $this->serverFingerprint,
        ]);

        if ($this->strictHostKeyChecking) {
            $this->verifyHostKey($connectionHost);
        }

        try {
            // Authenticate
            if ($this->useAgent) {
                $this->logger->debug("Authenticating with SSH agent for user: {user}", [
                    'user' => $this->username,
                ]);
                $authenticated = ssh2_auth_agent(
                    $this->sshConnection,
                    $this->username
                );
            } elseif ($this->useRsa) {
                $this->logger->debug("Authenticating with RSA key for user: {user}", [
                    'user' => $this->username,
                ]);
                $authenticated = ssh2_auth_pubkey_file(
                    $this->sshConnection,
                    $this->username,
                    $this->rsaPublicKey,
                    $this->rsaPrivateKey
                );
            } else {
                $this->logger->debug("Authenticating with password for user: {user}", [
                    'user' => $this->username,
                ]);
                if ($this->password === null) {
                    throw new AuthenticationException("Password not set for password authentication");
                }
                $authenticated = ssh2_auth_password(
                    $this->sshConnection,
                    $this->username,
                    $this->password
                );
            }

            if (!$authenticated) {
                $this->logger->warning("Authentication failed for user: {user} on {host}", [
                    'user' => $this->username,
                    'host' => $connectionHost,
                ]);
                throw new AuthenticationException("Authentication failed");
            }

            $this->logger->info("Successfully authenticated as {user} on {host}", [
                'user' => $this->username,
                'host' => $connectionHost,
            ]);

            // Create tunnel if using proxy
            if ($this->useProxy) {
                $this->logger->debug("Creating tunnel to final target {target}:{port}", [
                    'target' => $this->targetHost,
                    'port' => $this->sshPort,
                ]);
                $this->sshTunnel = ssh2_tunnel($this->sshConnection, $this->targetHost, $this->sshPort);
                if ($this->sshTunnel === false) {
                    $this->logger->error("Failed to create tunnel through proxy");
                    throw new ConnectionException("Failed to create tunnel through proxy");
                }
                $this->logger->debug("Tunnel successfully created");
            }
        } catch (AuthenticationException | ConnectionException $e) {
            ssh2_disconnect($this->sshConnection);
            $this->sshConnection = null;
            throw $e;
        }
    }

    /**
     * Close the SSH connection
     *
     * @return void
     */
    public function closeConnection(): void
    {
        if ($this->sshConnection !== null) {
            $this->logger->info("Closing SSH connection to {host}", ['host' => $this->targetHost]);
            ssh2_disconnect($this->sshConnection);
            $this->sshConnection = null;
        }
        $this->sshTunnel = null;
    }

    /**
     * Get the appropriate connection resource (tunnel or direct)
     *
     * @return mixed SSH connection resource
     * @throws ExecutionException if not connected
     */
    protected function getConnection()
    {
        if ($this->sshConnection === null) {
            throw new ExecutionException("Not connected. Call openConnection() first.");
        }
        return $this->useProxy ? $this->sshTunnel : $this->sshConnection;
    }

    /**
     * Execute a remote command via SSH
     *
     * @param string $command The command to execute
     * @return RemoteCommandOutput Command output with stdout, stderr, and exit code
     * @throws ExecutionException if command execution fails
     */
    public function remoteExec(string $command): RemoteCommandOutput
    {
        $connection = $this->getConnection();

        $this->logger->debug("Executing command: {command}", ['command' => $command]);

        $stream = ssh2_exec($connection, $command);
        if ($stream === false) {
            $this->logger->error("Failed to execute command: {command}", ['command' => $command]);
            throw new ExecutionException("Failed to execute command: $command");
        }

        $stderrStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($stream, true);
        if ($stderrStream !== false) {
            stream_set_blocking($stderrStream, true);
        }

        $stdout = stream_get_contents($stream);
        $stderr = $stderrStream !== false ? stream_get_contents($stderrStream) : '';

        $exitCode = ssh2_get_exit_status($stream);
        if ($exitCode === false) {
            $exitCode = 0;
        }

        fclose($stream);
        if ($stderrStream !== false) {
            fclose($stderrStream);
        }

        $result = new RemoteCommandOutput($stdout, $stderr, $exitCode);
        $this->logger->debug("Command executed with exit code {code}, stdout length {stdoutLength}, stderr length {stderrLength}", [
            'code' => $exitCode,
            'stdoutLength' => strlen($stdout),
            'stderrLength' => strlen($stderr),
        ]);

        return $result;
    }

    /**
     * Send a file via SCP
     *
     * @param string $localPath Local file path
     * @param string $remotePath Remote file path
     * @param string $permissions File permissions (e.g., "0644")
     * @return void
     * @throws ExecutionException if transfer fails
     */
    public function scpPut(string $localPath, string $remotePath, string $permissions = "0644"): void
    {
        $connection = $this->getConnection();

        InputValidator::validateFileReadable($localPath);

        $this->logger->info("Sending file via SCP: {local} -> {remote}", [
            'local' => $localPath,
            'remote' => $remotePath,
        ]);

        if (!file_exists($localPath)) {
            $this->logger->error("Local file not found: {path}", ['path' => $localPath]);
            throw new ExecutionException("Local file not found: $localPath");
        }

        $result = ssh2_scp_send($connection, $localPath, $remotePath, intval($permissions, 8));
        if ($result === false) {
            $this->logger->error("Failed to send file: {local} to {remote}", [
                'local' => $localPath,
                'remote' => $remotePath,
            ]);
            throw new ExecutionException("Failed to send file: $localPath to $remotePath");
        }

        $this->logger->info("File successfully sent: {local} -> {remote}", [
            'local' => $localPath,
            'remote' => $remotePath,
        ]);
    }

    /**
     * Receive a file via SCP
     *
     * @param string $remotePath Remote file path
     * @param string $localPath Local file path
     * @return void
     * @throws ExecutionException if transfer fails
     */
    public function scpGet(string $remotePath, string $localPath): void
    {
        $connection = $this->getConnection();

        $this->logger->info("Receiving file via SCP: {remote} -> {local}", [
            'remote' => $remotePath,
            'local' => $localPath,
        ]);

        $result = ssh2_scp_recv($connection, $remotePath, $localPath);
        if ($result === false) {
            $this->logger->error("Failed to retrieve file: {remote} to {local}", [
                'remote' => $remotePath,
                'local' => $localPath,
            ]);
            throw new ExecutionException("Failed to retrieve file: $remotePath to $localPath");
        }

        $this->logger->info("File successfully received: {remote} -> {local}", [
            'remote' => $remotePath,
            'local' => $localPath,
        ]);
    }

    /**
     * Initialize the SFTP subsystem
     *
     * @return resource SFTP resource
     * @throws ExecutionException if SFTP initialization fails
     */
    protected function initializeSftp()
    {
        if ($this->sftp !== null) {
            return $this->sftp;
        }

        $connection = $this->getConnection();

        $this->logger->debug("Initializing SFTP subsystem");

        $sftp = ssh2_sftp($connection);
        if ($sftp === false) {
            $this->logger->error("Failed to initialize SFTP subsystem");
            throw new ExecutionException("Failed to initialize SFTP subsystem");
        }

        $this->sftp = $sftp;
        $this->logger->debug("SFTP subsystem initialized");

        return $this->sftp;
    }

    /**
     * Send a file via SFTP
     *
     * @param string $localPath Local file path
     * @param string $remotePath Remote file path
     * @return void
     * @throws ExecutionException if transfer fails
     */
    public function sftpPut(string $localPath, string $remotePath): void
    {
        InputValidator::validateFileReadable($localPath);

        $this->logger->info("Sending file via SFTP: {local} -> {remote}", [
            'local' => $localPath,
            'remote' => $remotePath,
        ]);

        $remoteContent = file_get_contents($localPath);
        if ($remoteContent === false) {
            $this->logger->error("Failed to read local file: {path}", ['path' => $localPath]);
            throw new ExecutionException("Failed to read local file: $localPath");
        }

        $sftp = $this->initializeSftp();
        $remoteStream = @fopen('ssh2.sftp://' . intval($sftp) . $remotePath, 'w');
        if ($remoteStream === false) {
            $this->logger->error("Failed to open remote file for writing: {path}", ['path' => $remotePath]);
            throw new ExecutionException("Failed to open remote file for writing: $remotePath");
        }

        $written = fwrite($remoteStream, $remoteContent);
        fclose($remoteStream);

        if ($written === false) {
            $this->logger->error("Failed to write remote file: {path}", ['path' => $remotePath]);
            throw new ExecutionException("Failed to write remote file: $remotePath");
        }

        $this->logger->info("File successfully sent via SFTP: {local} -> {remote}", [
            'local' => $localPath,
            'remote' => $remotePath,
        ]);
    }

    /**
     * Receive a file via SFTP
     *
     * @param string $remotePath Remote file path
     * @param string $localPath Local file path
     * @return void
     * @throws ExecutionException if transfer fails
     */
    public function sftpGet(string $remotePath, string $localPath): void
    {
        $this->logger->info("Receiving file via SFTP: {remote} -> {local}", [
            'remote' => $remotePath,
            'local' => $localPath,
        ]);

        $sftp = $this->initializeSftp();
        $remoteContent = @file_get_contents('ssh2.sftp://' . intval($sftp) . $remotePath);
        if ($remoteContent === false) {
            $this->logger->error("Failed to read remote file via SFTP: {path}", ['path' => $remotePath]);
            throw new ExecutionException("Failed to read remote file: $remotePath");
        }

        $written = file_put_contents($localPath, $remoteContent);
        if ($written === false) {
            $this->logger->error("Failed to write local file: {path}", ['path' => $localPath]);
            throw new ExecutionException("Failed to write local file: $localPath");
        }

        $this->logger->info("File successfully received via SFTP: {remote} -> {local}", [
            'remote' => $remotePath,
            'local' => $localPath,
        ]);
    }

    /**
     * Destructor - ensure connection is closed
     */
    public function __destruct()
    {
        $this->closeConnection();
    }

    /**
     * Get the logger instance
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Set a different logger instance
     *
     * @param LoggerInterface $logger
     * @return self
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Escape a shell argument for safe remote execution
     *
     * @param string $argument
     * @return string
     */
    protected static function escapeShellArgument(string $argument): string
    {
        return escapeshellarg($argument);
    }
}
