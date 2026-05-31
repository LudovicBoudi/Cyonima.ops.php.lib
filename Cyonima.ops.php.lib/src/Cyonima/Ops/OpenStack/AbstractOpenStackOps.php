<?php

declare(strict_types=1);

namespace Cyonima\Ops\OpenStack;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for OpenStack operations via the OpenStack CLI over SSH.
 *
 * Requires the `openstack` CLI to be installed and available in the remote
 * environment's PATH.
 */
abstract class AbstractOpenStackOps extends AbstractOps
{
    private ?string $authUrl = null;
    private ?string $projectName = null;
    private ?string $username = null;
    private ?string $password = null;
    private string $userDomainName = 'Default';
    private string $projectDomainName = 'Default';

    /**
     * Set OpenStack authentication details to use on subsequent commands.
     *
     * @param string $authUrl
     * @param string $projectName
     * @param string $username
     * @param string $password
     * @param string $userDomainName
     * @param string $projectDomainName
     * @return self
     */
    public function setOpenStackAuthentication(
        string $authUrl,
        string $projectName,
        string $username,
        string $password,
        string $userDomainName = 'Default',
        string $projectDomainName = 'Default'
    ): self {
        $this->authUrl = $authUrl;
        $this->projectName = $projectName;
        $this->username = $username;
        $this->password = $password;
        $this->userDomainName = $userDomainName;
        $this->projectDomainName = $projectDomainName;

        return $this;
    }

    /**
     * Authenticate against an OpenStack identity endpoint.
     *
     * @return RemoteCommandOutput
     */
    public function authenticate(): RemoteCommandOutput
    {
        return $this->openstackCommand('token issue');
    }

    /**
     * Get OpenStack CLI version.
     *
     * @return RemoteCommandOutput
     */
    public function getOpenStackVersion(): RemoteCommandOutput
    {
        return $this->openstackCommand('--version');
    }

    /**
     * List available projects.
     *
     * @return RemoteCommandOutput
     */
    public function listProjects(): RemoteCommandOutput
    {
        return $this->openstackCommand('project list --format json');
    }

    /**
     * List available flavors.
     *
     * @return RemoteCommandOutput
     */
    public function listFlavors(): RemoteCommandOutput
    {
        return $this->openstackCommand('flavor list --format json');
    }

    /**
     * List available images.
     *
     * @return RemoteCommandOutput
     */
    public function listImages(): RemoteCommandOutput
    {
        return $this->openstackCommand('image list --format json');
    }

    /**
     * List servers.
     *
     * @param string|null $projectName Optional project filter
     * @return RemoteCommandOutput
     */
    public function listServers(?string $projectName = null): RemoteCommandOutput
    {
        $command = 'server list --long --format json';
        if ($projectName !== null && trim($projectName) !== '') {
            $command .= ' --project ' . self::escapeShellArgument($projectName);
        }

        return $this->openstackCommand($command);
    }

    /**
     * List networks.
     *
     * @return RemoteCommandOutput
     */
    public function listNetworks(): RemoteCommandOutput
    {
        return $this->openstackCommand('network list --format json');
    }

    /**
     * Create a server.
     *
     * @param string $name
     * @param string $image
     * @param string $flavor
     * @param string $network
     * @param string $keyName
     * @param string|null $securityGroup
     * @return RemoteCommandOutput
     */
    public function createServer(
        string $name,
        string $image,
        string $flavor,
        string $network,
        string $keyName,
        ?string $securityGroup = null
    ): RemoteCommandOutput {
        $command = 'server create --wait '
            . self::escapeShellArgument($name)
            . ' --image ' . self::escapeShellArgument($image)
            . ' --flavor ' . self::escapeShellArgument($flavor)
            . ' --network ' . self::escapeShellArgument($network)
            . ' --key-name ' . self::escapeShellArgument($keyName)
            . ' --format json';

        if ($securityGroup !== null && trim($securityGroup) !== '') {
            $command .= ' --security-group ' . self::escapeShellArgument($securityGroup);
        }

        return $this->openstackCommand($command);
    }

    /**
     * Start a server.
     *
     * @param string $name
     * @return RemoteCommandOutput
     */
    public function startServer(string $name): RemoteCommandOutput
    {
        return $this->openstackCommand('server start ' . self::escapeShellArgument($name));
    }

    /**
     * Stop a server.
     *
     * @param string $name
     * @return RemoteCommandOutput
     */
    public function stopServer(string $name): RemoteCommandOutput
    {
        return $this->openstackCommand('server stop ' . self::escapeShellArgument($name));
    }

    /**
     * Delete a server.
     *
     * @param string $name
     * @return RemoteCommandOutput
     */
    public function deleteServer(string $name): RemoteCommandOutput
    {
        return $this->openstackCommand('server delete ' . self::escapeShellArgument($name) . ' --wait');
    }

    /**
     * Execute an OpenStack CLI subcommand with the current authentication context.
     *
     * @param string $subcommand
     * @return RemoteCommandOutput
     */
    protected function openstackCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('openstack ' . $this->buildAuthOptions() . ' ' . $subcommand);
    }

    /**
     * Build auth options for the OpenStack CLI.
     *
     * @return string
     */
    private function buildAuthOptions(): string
    {
        if ($this->authUrl === null || $this->projectName === null || $this->username === null || $this->password === null) {
            throw new \RuntimeException('OpenStack authentication has not been configured. Call setOpenStackAuthentication() first.');
        }

        return '--os-auth-url ' . self::escapeShellArgument($this->authUrl)
            . ' --os-project-name ' . self::escapeShellArgument($this->projectName)
            . ' --os-username ' . self::escapeShellArgument($this->username)
            . ' --os-password ' . self::escapeShellArgument($this->password)
            . ' --os-user-domain-name ' . self::escapeShellArgument($this->userDomainName)
            . ' --os-project-domain-name ' . self::escapeShellArgument($this->projectDomainName);
    }
}
