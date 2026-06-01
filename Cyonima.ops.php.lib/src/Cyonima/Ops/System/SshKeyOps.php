<?php

declare(strict_types=1);

namespace Cyonima\Ops\System;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * SSH authorized_keys management module
 *
 * Provides methods to manage SSH public keys in authorized_keys files
 * on remote systems via SSH.
 */
class SshKeyOps extends AbstractOps
{
    /**
     * Add a public key to a user's authorized_keys
     *
     * Creates the .ssh directory if it does not exist and appends the
     * public key. The public key is base64-encoded for safe transport.
     *
     * @param string $username The user to add the key for
     * @param string $publicKey The public key string (e.g. "ssh-ed25519 AAAA... comment")
     * @return RemoteCommandOutput
     */
    public function addAuthorizedKey(string $username, string $publicKey): RemoteCommandOutput
    {
        $encodedKey = base64_encode($publicKey);

        return $this->remoteExec(
            'mkdir -p ~' . $username . '/.ssh'
            . ' && chmod 700 ~' . $username . '/.ssh'
            . ' && echo ' . $encodedKey . ' | base64 --decode >> ~' . $username . '/.ssh/authorized_keys'
            . ' && chmod 600 ~' . $username . '/.ssh/authorized_keys'
        );
    }

    /**
     * Remove authorized keys matching a comment
     *
     * Lines containing the comment string are removed from the
     * authorized_keys file via grep -v.
     *
     * @param string $username The user to remove the key for
     * @param string $keyComment The comment string to match for removal
     * @return RemoteCommandOutput
     */
    public function removeAuthorizedKey(string $username, string $keyComment): RemoteCommandOutput
    {
        return $this->remoteExec(
            'grep -v ' . self::escapeShellArgument($keyComment) . ' ~' . $username . '/.ssh/authorized_keys'
            . ' > /tmp/authorized_keys_new'
            . ' && mv /tmp/authorized_keys_new ~' . $username . '/.ssh/authorized_keys'
        );
    }

    /**
     * List all authorized keys for a user
     *
     * @param string $username The user to list keys for
     * @return RemoteCommandOutput
     */
    public function listAuthorizedKeys(string $username): RemoteCommandOutput
    {
        return $this->remoteExec('cat ~' . $username . '/.ssh/authorized_keys');
    }

    /**
     * Generate a new SSH key pair
     *
     * Generates a key pair with an empty passphrase using ssh-keygen.
     *
     * @param string $path Remote path for the generated key (without .pub suffix)
     * @param string $comment Optional key comment
     * @param string $type Key type, either 'ed25519' (default) or 'rsa'
     * @return RemoteCommandOutput
     */
    public function generateKeyPair(string $path, string $comment = '', string $type = 'ed25519'): RemoteCommandOutput
    {
        $command = 'ssh-keygen -t ' . self::escapeShellArgument($type)
            . ' -f ' . self::escapeShellArgument($path)
            . ' -N ""';

        if ($comment !== '') {
            $command .= ' -C ' . self::escapeShellArgument($comment);
        }

        return $this->remoteExec($command);
    }

    /**
     * Read the public key for a given key path
     *
     * @param string $path Path to the private key (the .pub file will be appended)
     * @return RemoteCommandOutput
     */
    public function readPublicKey(string $path): RemoteCommandOutput
    {
        return $this->remoteExec('cat ' . self::escapeShellArgument($path) . '.pub');
    }

    /**
     * Replace the entire authorized_keys file for a user
     *
     * The content is base64-encoded for safe transport.
     *
     * @param string $username The user to set keys for
     * @param string $content Full authorized_keys content to write
     * @return RemoteCommandOutput
     */
    public function setAuthorizedKeys(string $username, string $content): RemoteCommandOutput
    {
        $encodedContent = base64_encode($content);

        return $this->remoteExec(
            'echo ' . $encodedContent . ' | base64 --decode > ~' . $username . '/.ssh/authorized_keys'
        );
    }

    /**
     * Add restrictions (command=, from=, etc.) to a key by comment
     *
     * Uses sed to prepend restriction options to the line matching the
     * given comment in the authorized_keys file.
     *
     * @param string $username The user whose key to restrict
     * @param string $keyComment The comment identifying the key to restrict
     * @param string $restrictions Restrictions to prepend (e.g. 'command="/usr/bin/rsync"')
     * @return RemoteCommandOutput
     */
    public function restrictKey(string $username, string $keyComment, string $restrictions): RemoteCommandOutput
    {
        $escapedComment = self::escapeShellArgument($keyComment);
        $escapedRestrictions = self::escapeShellArgument($restrictions);

        return $this->remoteExec(
            'sed -i /' . $escapedComment . '/s/^/' . $escapedRestrictions . ' / ~' . $username . '/.ssh/authorized_keys'
        );
    }
}
