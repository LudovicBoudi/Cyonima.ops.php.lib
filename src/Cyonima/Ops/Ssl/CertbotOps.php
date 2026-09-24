<?php

declare(strict_types=1);

namespace Cyonima\Ops\Ssl;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Certbot (Let's Encrypt) SSL certificate operations over SSH
 *
 * Provides a direct interface to manage Let's Encrypt SSL certificates
 * using Certbot, including obtaining, renewing, revoking, and inspecting
 * certificates.
 *
 * Example usage:
 * ```php
 * $certbot = new CertbotOps();
 * $certbot->setHost('web.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $certbot->openConnection();
 * $certbot->obtainCertificate('example.com', 'admin@example.com');
 * $certbot->listCertificates();
 * $certbot->closeConnection();
 * ```
 */
class CertbotOps extends AbstractOps
{
    /**
     * Get the installed Certbot version.
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('certbot --version 2>&1');
    }

    /**
     * List all certificates managed by Certbot.
     *
     * @return RemoteCommandOutput
     */
    public function listCertificates(): RemoteCommandOutput
    {
        return $this->remoteExec('certbot certificates');
    }

    /**
     * Obtain a new SSL certificate using the standalone authenticator.
     *
     * @param string $domain Domain name for the certificate
     * @param string $email Email address for account registration and recovery
     * @param bool $dryRun Whether to perform a dry run (default: false)
     * @return RemoteCommandOutput
     */
    public function obtainCertificate(string $domain, string $email, bool $dryRun = false): RemoteCommandOutput
    {
        $domain = self::escapeShellArgument($domain);
        $email = self::escapeShellArgument($email);
        $command = 'certbot certonly --standalone -d ' . $domain . ' --non-interactive --agree-tos -m ' . $email;
        if ($dryRun) {
            $command .= ' --dry-run';
        }
        return $this->remoteExec($command);
    }

    /**
     * Obtain a new SSL certificate using the Nginx authenticator.
     *
     * @param string $domain Domain name for the certificate
     * @param string $email Email address for account registration and recovery
     * @param bool $dryRun Whether to perform a dry run (default: false)
     * @return RemoteCommandOutput
     */
    public function obtainWithNginx(string $domain, string $email, bool $dryRun = false): RemoteCommandOutput
    {
        $domain = self::escapeShellArgument($domain);
        $email = self::escapeShellArgument($email);
        $command = 'certbot --nginx -d ' . $domain . ' --non-interactive --agree-tos -m ' . $email;
        if ($dryRun) {
            $command .= ' --dry-run';
        }
        return $this->remoteExec($command);
    }

    /**
     * Renew all existing certificates managed by Certbot.
     *
     * @param bool $dryRun Whether to perform a dry run (default: false)
     * @return RemoteCommandOutput
     */
    public function renewCertificates(bool $dryRun = false): RemoteCommandOutput
    {
        $command = 'certbot renew --non-interactive';
        if ($dryRun) {
            $command .= ' --dry-run';
        }
        return $this->remoteExec($command);
    }

    /**
     * Delete a certificate managed by Certbot.
     *
     * @param string $domain Domain name of the certificate to delete
     * @return RemoteCommandOutput
     */
    public function deleteCertificate(string $domain): RemoteCommandOutput
    {
        $domain = self::escapeShellArgument($domain);
        return $this->remoteExec('certbot delete --cert-name ' . $domain . ' --non-interactive');
    }

    /**
     * Get detailed information about a specific certificate.
     *
     * @param string $domain Domain name of the certificate
     * @return RemoteCommandOutput
     */
    public function getCertificateDetails(string $domain): RemoteCommandOutput
    {
        $domain = self::escapeShellArgument($domain);
        return $this->remoteExec('certbot certificates --cert-name ' . $domain);
    }

    /**
     * Revoke a certificate managed by Certbot.
     *
     * @param string $domain Domain name of the certificate to revoke
     * @return RemoteCommandOutput
     */
    public function revokeCertificate(string $domain): RemoteCommandOutput
    {
        $domain = self::escapeShellArgument($domain);
        return $this->remoteExec('certbot revoke --cert-name ' . $domain . ' --non-interactive');
    }

    /**
     * Register a new Let's Encrypt account.
     *
     * @param string $email Email address for the account
     * @return RemoteCommandOutput
     */
    public function registerAccount(string $email): RemoteCommandOutput
    {
        $email = self::escapeShellArgument($email);
        return $this->remoteExec('certbot register --non-interactive -m ' . $email . ' --agree-tos');
    }

    /**
     * Update the email address associated with the Let's Encrypt account.
     *
     * @param string $email New email address
     * @return RemoteCommandOutput
     */
    public function updateAccount(string $email): RemoteCommandOutput
    {
        $email = self::escapeShellArgument($email);
        return $this->remoteExec('certbot update_account --non-interactive -m ' . $email);
    }

    /**
     * Show the current Let's Encrypt account information.
     *
     * @return RemoteCommandOutput
     */
    public function getAccountInfo(): RemoteCommandOutput
    {
        return $this->remoteExec('certbot show_account');
    }

    /**
     * Get the expected path to a certificate's fullchain.pem file.
     *
     * This does not check whether the file exists; it only outputs the
     * standard Let's Encrypt path for the given domain.
     *
     * @param string $domain Domain name
     * @return RemoteCommandOutput
     */
    public function certificatePath(string $domain): RemoteCommandOutput
    {
        $domain = self::escapeShellArgument($domain);
        $path = self::escapeShellArgument('/etc/letsencrypt/live/' . $domain . '/fullchain.pem');
        return $this->remoteExec('echo ' . $path);
    }

    /**
     * Get the expected path to a certificate's privkey.pem file.
     *
     * This does not check whether the file exists; it only outputs the
     * standard Let's Encrypt path for the given domain.
     *
     * @param string $domain Domain name
     * @return RemoteCommandOutput
     */
    public function certificateKeyPath(string $domain): RemoteCommandOutput
    {
        $domain = self::escapeShellArgument($domain);
        $path = self::escapeShellArgument('/etc/letsencrypt/live/' . $domain . '/privkey.pem');
        return $this->remoteExec('echo ' . $path);
    }
}
