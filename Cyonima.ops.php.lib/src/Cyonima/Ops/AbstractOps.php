<?php

declare(strict_types=1);

namespace Cyonima\Ops;

use Cyonima\Ops\Exception\{ConnectionException, AuthenticationException, ExecutionException};
use Cyonima\Ops\Logger\SimpleLogger;
use Psr\Log\LoggerInterface;
use php2-ssh2\ssh2_connect;
use php2-ssh2\ssh2_tunnel;
use php2-ssh2\ssh2_auth_password;
use php2-ssh2\ssh2_auth_pubkey_file;
use php2-ssh2\ssh2_exec;
use php2-ssh2\ssh2_scp_send;
use php2-ssh2\ssh2_scp_recv;
use php2-ssh2\ssh2_disconnect;

/**
 * Abstract base class for OPS operations on infrastructure components
 *
 * This class provides core SSH connectivity and command execution functionality.
 * Note: This library does not check for injection attacks. It is the developer's
 * responsibility to validate all input variables before using them.
 */
abstract class AbstractOps
{
    private bool $useProxy = false;
    private ?string $proxyHost = null;
    private bool $useRsa = false;
    private ?string $rsaPrivateKey = null;
    private ?string $rsaPublicKey = null;
    private int $sshPort = 22;
    private $sshConnection = null;
    private ?string $targetHost = null;
    private ?string $username = null;
    private ?string $password = null;
    private $sshTunnel = null;
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface|null $logger Optional PSR-3 logger (default: SimpleLogger to stderr)
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new SimpleLogger();
        $this->initialize();
    }

    /**
     * Initialize all properties to their default values
     *
     * @return void
     */
    private function initialize(): void
    {
        $this->useProxy = false;
        $this->proxyHost = null;
        $this->useRsa = false;
        $this->rsaPrivateKey = null;
        $this->rsaPublicKey = null;
        $this->sshPort = 22;
        $this->sshConnection = null;
        $this->targetHost = null;
        $this->username = null;
        $this->password = null;
        $this->sshTunnel = null;
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

        // Establish connection
        $this->sshConnection = ssh2_connect($connectionHost, $this->sshPort);
        if ($this->sshConnection === false) {
            $this->logger->error("Failed to connect to {host}:{port}", [
                'host' => $connectionHost,
                'port' => $this->sshPort,
            ]);
            throw new ConnectionException("Failed to connect to $connectionHost");
        }

        $this->logger->debug("SSH connection established to {host}", ['host' => $connectionHost]);

        try {
            // Authenticate
            if ($this->useRsa) {
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

        stream_set_blocking($stream, true);
        $output = "";
        while ($chunk = fread($stream, 4096)) {
            $output .= $chunk;
        }
        fclose($stream);

        $result = new RemoteCommandOutput($output, '', 0);
        $this->logger->debug("Command executed successfully, output length: {length} bytes", [
            'length' => strlen($output),
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
}
