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
 *
 * Security note: the higher-level operation methods (in the Linux/BSD/Windows/etc.
 * subclasses and traits) escape their arguments via escapeShellArgument() or
 * escapePowerShellArgument() before building the remote command. However,
 * remoteExec() executes the command string verbatim: when calling it directly,
 * it is the caller's responsibility to validate and escape any untrusted input
 * (see InputValidator).
 */
abstract class AbstractOps
{
    public const int DEFAULT_SSH_PORT = 22;
    public const int DEFAULT_SSH_TIMEOUT = 30;

    private bool $useProxy = false;
    private ?string $proxyHost = null;
    private int $proxyTargetPort = self::DEFAULT_SSH_PORT;
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
     * The SSH session is established with the jump host, and commands are relayed
     * to the final target (set via setHost()) by invoking `ssh` on the jump host.
     * See buildProxyCommand() for the authentication requirements. The target SSH
     * port defaults to 22 and can be changed with setProxyTargetPort().
     *
     * Note: SCP/SFTP file transfers are not supported in proxy mode.
     *
     * @param string $proxyHost IP address or hostname of the jump host
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
     * Set the SSH port used to reach the final target from the jump host
     *
     * Only relevant in proxy mode. Defaults to 22.
     *
     * @param int $port SSH port of the target
     * @return self Fluent interface
     */
    public function setProxyTargetPort(int $port): self
    {
        InputValidator::validateSshPort($port);
        $this->proxyTargetPort = $port;
        $this->logger->debug("Proxy target port set to: {port}", ['port' => $port]);
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
     * Compares the SHA-1 fingerprint of the connected server's host key against
     * the keys recorded for this host in the known_hosts file. If a matching host
     * entry exists but no key matches, the connection is rejected (possible
     * man-in-the-middle attack). If no entry exists for the host at all, the
     * connection is also rejected (strict checking).
     *
     * @param string $host The hostname or IP to verify
     * @return void
     * @throws ConnectionException if verification fails
     */
    private function verifyHostKey(string $host): void
    {
        if ($this->serverFingerprint === null) {
            throw new ConnectionException("Cannot verify host key: server fingerprint unavailable");
        }

        $knownHosts = $this->knownHostsFile ?? ($_SERVER['HOME'] ?? '~') . '/.ssh/known_hosts';
        $knownHosts = str_replace('~', $_SERVER['HOME'] ?? '/root', $knownHosts);

        if (!file_exists($knownHosts) || !is_readable($knownHosts)) {
            throw new ConnectionException(
                "Strict host key checking is enabled but the known hosts file is missing or unreadable: $knownHosts"
            );
        }

        $lines = file($knownHosts, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new ConnectionException("Unable to read known hosts file: $knownHosts");
        }

        // ssh2_fingerprint(SSH2_FINGERPRINT_SHA1 | SSH2_FINGERPRINT_HEX) returns the
        // SHA-1 digest (hex) of the raw host key blob - the same blob that is stored,
        // base64-encoded, as the third field of a known_hosts entry.
        $serverFingerprint = strtoupper($this->serverFingerprint);
        $hostFound = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Format: <host-pattern> <key-type> <base64-key> [comment]
            $parts = preg_split('/\s+/', $line);
            if ($parts === false || count($parts) < 3) {
                continue;
            }

            [$hostPattern, $keyType, $keyBlob] = $parts;

            if (!$this->hostMatchesPattern($host, $hostPattern)) {
                continue;
            }

            $rawKey = base64_decode($keyBlob, true);
            if ($rawKey === false || $rawKey === '') {
                continue;
            }

            $hostFound = true;
            $expectedFingerprint = strtoupper(sha1($rawKey));

            if (hash_equals($expectedFingerprint, $serverFingerprint)) {
                $this->logger->debug("Host key verified for {host} (type: {type})", [
                    'host' => $host,
                    'type' => $keyType,
                ]);
                return;
            }
        }

        if ($hostFound) {
            $this->logger->error("Host key verification FAILED for {host}: fingerprint mismatch", [
                'host' => $host,
            ]);
            throw new ConnectionException(
                "Host key verification failed for $host: the server's key does not match any recorded key " .
                "in $knownHosts. This may indicate a man-in-the-middle attack."
            );
        }

        $this->logger->error("Host {host} has no entry in known hosts file", ['host' => $host]);
        throw new ConnectionException(
            "Host key verification failed: no entry for $host in $knownHosts. " .
            "Add the host key to the known hosts file or disable strict host key checking."
        );
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

        // Note: ext-ssh2 has no native connect timeout option (the third argument of
        // ssh2_connect is reserved for key-exchange method negotiation). The configured
        // timeout is applied to command execution streams instead (see remoteExec()).
        $this->sshConnection = @ssh2_connect($connectionHost, $this->sshPort);
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

        try {
            // Verify the host key before sending any credentials
            if ($this->strictHostKeyChecking) {
                $this->verifyHostKey($connectionHost);
            }

            // Authenticate
            if ($this->useAgent) {
                $this->logger->debug("Authenticating with SSH agent for user: {user}", [
                    'user' => $this->username,
                ]);
                $authenticated = @ssh2_auth_agent(
                    $this->sshConnection,
                    $this->username
                );
            } elseif ($this->useRsa) {
                $this->logger->debug("Authenticating with RSA key for user: {user}", [
                    'user' => $this->username,
                ]);
                $authenticated = @ssh2_auth_pubkey_file(
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
                $authenticated = @ssh2_auth_password(
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

            // In proxy (jump host) mode the SSH session is established with the jump
            // host above. Commands are then executed on the final target by invoking
            // `ssh` on the jump host (see buildProxyCommand()). No tunnel resource is
            // created, because ext-ssh2 cannot layer a new SSH session over one.
            if ($this->useProxy) {
                $this->logger->debug("Proxy mode: commands will be relayed to {target}:{port} via jump host {jump}", [
                    'target' => $this->targetHost,
                    'port' => $this->proxyTargetPort,
                    'jump' => $this->proxyHost,
                ]);
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
    }

    /**
     * Get the active SSH connection resource
     *
     * In proxy (jump host) mode this is the session to the jump host; commands
     * are relayed to the final target via buildProxyCommand().
     *
     * @return mixed SSH connection resource
     * @throws ExecutionException if not connected
     */
    protected function getConnection()
    {
        if ($this->sshConnection === null) {
            throw new ExecutionException("Not connected. Call openConnection() first.");
        }
        return $this->sshConnection;
    }

    /**
     * Wrap a command so it runs on the final target through the jump host
     *
     * The SSH session is connected to the jump host; this builds an `ssh`
     * invocation, executed on the jump host, that connects to the target and
     * runs the requested command.
     *
     * Authentication from the jump host to the target:
     * - When password authentication is used, the password is passed to `sshpass`
     *   through a temporary file (chmod 400, removed afterwards), so it never
     *   appears in the jump host process list. This requires `sshpass` to be
     *   installed on the jump host.
     * - When RSA key or SSH agent authentication is used, the jump host is expected
     *   to have its own key-based access to the target (BatchMode is enabled to
     *   avoid interactive prompts). The client's local private key is not pushed to
     *   the jump host.
     *
     * The target user defaults to the configured username; the target port
     * defaults to 22 and can be changed with setProxyTargetPort().
     *
     * @param string $command The command to run on the target
     * @return string The command to execute on the jump host
     */
    private function buildProxyCommand(string $command): string
    {
        $target = ($this->username ?? '') . '@' . ($this->targetHost ?? '');
        $usePassword = !$this->useRsa && !$this->useAgent && $this->password !== null;

        $sshOptions = '-o StrictHostKeyChecking=accept-new'
            . ' -o ConnectTimeout=' . $this->sshTimeout
            . ' -p ' . $this->proxyTargetPort;

        // For key/agent access, fail fast instead of hanging on a prompt.
        // BatchMode must NOT be set for the password path, as it disables the
        // interactive prompt that sshpass answers.
        if (!$usePassword) {
            $sshOptions .= ' -o BatchMode=yes';
        }

        $sshInvocation = 'ssh ' . $sshOptions
            . ' ' . self::escapeShellArgument($target)
            . ' ' . self::escapeShellArgument($command);

        // Password auth to the target: feed the password to sshpass from a
        // restricted temporary file on the jump host, never via the command line.
        if ($usePassword) {
            $encoded = base64_encode((string) $this->password);
            $tmpFile = '/tmp/._pxy_' . bin2hex(random_bytes(8));
            $tmp = self::escapeShellArgument($tmpFile);

            // Preserve the target command's exit code (the trailing rm must not mask it).
            return 'echo ' . self::escapeShellArgument($encoded)
                . ' | base64 --decode > ' . $tmp
                . ' && chmod 400 ' . $tmp
                . '; sshpass -f ' . $tmp . ' ' . $sshInvocation
                . '; __rc=$?; rm -f ' . $tmp . '; exit $__rc';
        }

        // Key/agent based access from the jump host to the target.
        return $sshInvocation;
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

        // In proxy mode, relay the command to the final target through the jump host.
        $effectiveCommand = $this->useProxy ? $this->buildProxyCommand($command) : $command;

        $this->logger->debug("Executing command: {command}", ['command' => $command]);

        $stream = @ssh2_exec($connection, $effectiveCommand);
        if ($stream === false) {
            $this->logger->error("Failed to execute command: {command}", ['command' => $command]);
            throw new ExecutionException("Failed to execute command: $command");
        }

        $stderrStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        stream_set_blocking($stream, true);
        stream_set_timeout($stream, $this->sshTimeout);
        if ($stderrStream !== false) {
            stream_set_blocking($stderrStream, true);
            stream_set_timeout($stderrStream, $this->sshTimeout);
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
     * Guard against file transfers in proxy (jump host) mode
     *
     * The jump host rebound only relays command execution; SCP/SFTP would operate
     * against the jump host, not the target, so they are rejected explicitly.
     *
     * @return void
     * @throws ExecutionException if proxy mode is enabled
     */
    private function assertNoProxyForFileTransfer(): void
    {
        if ($this->useProxy) {
            throw new ExecutionException(
                "File transfer (SCP/SFTP) is not supported in proxy (jump host) mode. " .
                "Connect directly to the target host to transfer files."
            );
        }
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
        $this->assertNoProxyForFileTransfer();
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
        $this->assertNoProxyForFileTransfer();
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
        $this->assertNoProxyForFileTransfer();

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
