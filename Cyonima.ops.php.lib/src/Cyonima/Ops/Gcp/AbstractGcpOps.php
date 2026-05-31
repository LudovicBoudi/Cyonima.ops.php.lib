<?php

declare(strict_types=1);

namespace Cyonima\Ops\Gcp;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for Google Cloud Platform management via gcloud CLI over SSH.
 *
 * Requires the Google Cloud SDK (`gcloud`) to be installed and available in the
 * remote environment's PATH.
 */
abstract class AbstractGcpOps extends AbstractOps
{
    private ?string $projectId = null;

    /**
     * Set the active GCP project.
     *
     * @param string $projectId
     * @return self
     */
    public function setProject(string $projectId): self
    {
        $this->projectId = $projectId;
        return $this;
    }

    /**
     * Execute a gcloud CLI subcommand.
     *
     * @param string $subcommand
     * @return RemoteCommandOutput
     */
    protected function gcloudCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('gcloud ' . $subcommand);
    }

    /**
     * Get the installed gcloud version.
     *
     * @return RemoteCommandOutput
     */
    public function getGcloudVersion(): RemoteCommandOutput
    {
        return $this->gcloudCommand('version --format=json');
    }

    /**
     * Authenticate using a service account key file.
     *
     * @param string $keyFilePath Local path to the service account key on the remote host
     * @return RemoteCommandOutput
     */
    public function authenticateWithServiceAccountKey(string $keyFilePath): RemoteCommandOutput
    {
        return $this->gcloudCommand('auth activate-service-account --key-file ' . self::escapeShellArgument($keyFilePath));
    }

    /**
     * List available Google Cloud projects.
     *
     * @return RemoteCommandOutput
     */
    public function listProjects(): RemoteCommandOutput
    {
        return $this->gcloudCommand('projects list --format=json');
    }

    /**
     * List available GCP regions.
     *
     * @return RemoteCommandOutput
     */
    public function listRegions(): RemoteCommandOutput
    {
        return $this->gcloudCommand('compute regions list --format=json');
    }

    /**
     * List available GCP zones.
     *
     * @return RemoteCommandOutput
     */
    public function listZones(): RemoteCommandOutput
    {
        return $this->gcloudCommand('compute zones list --format=json');
    }

    /**
     * List virtual machine instances.
     *
     * @param string|null $zone Optional zone filter
     * @return RemoteCommandOutput
     */
    public function listInstances(?string $zone = null): RemoteCommandOutput
    {
        $command = 'compute instances list --format=json';
        if ($zone !== null && trim($zone) !== '') {
            $command .= ' --zones ' . self::escapeShellArgument($zone);
        }

        return $this->gcloudCommand($command);
    }

    /**
     * Create a compute instance.
     *
     * @param string $name
     * @param string $zone
     * @param string $machineType
     * @param string $imageFamily
     * @param string $imageProject
     * @param string $network
     * @param string|null $subnetwork
     * @return RemoteCommandOutput
     */
    public function createInstance(
        string $name,
        string $zone,
        string $machineType,
        string $imageFamily,
        string $imageProject,
        string $network,
        ?string $subnetwork = null
    ): RemoteCommandOutput {
        $command = 'compute instances create ' . self::escapeShellArgument($name)
            . ' --zone ' . self::escapeShellArgument($zone)
            . ' --machine-type ' . self::escapeShellArgument($machineType)
            . ' --image-family ' . self::escapeShellArgument($imageFamily)
            . ' --image-project ' . self::escapeShellArgument($imageProject)
            . ' --network ' . self::escapeShellArgument($network)
            . ' --format=json';

        if ($subnetwork !== null && trim($subnetwork) !== '') {
            $command .= ' --subnet ' . self::escapeShellArgument($subnetwork);
        }

        return $this->gcloudCommand($command);
    }

    /**
     * Start a compute instance.
     *
     * @param string $name
     * @param string $zone
     * @return RemoteCommandOutput
     */
    public function startInstance(string $name, string $zone): RemoteCommandOutput
    {
        return $this->gcloudCommand('compute instances start ' . self::escapeShellArgument($name)
            . ' --zone ' . self::escapeShellArgument($zone));
    }

    /**
     * Stop a compute instance.
     *
     * @param string $name
     * @param string $zone
     * @return RemoteCommandOutput
     */
    public function stopInstance(string $name, string $zone): RemoteCommandOutput
    {
        return $this->gcloudCommand('compute instances stop ' . self::escapeShellArgument($name)
            . ' --zone ' . self::escapeShellArgument($zone));
    }

    /**
     * Delete a compute instance.
     *
     * @param string $name
     * @param string $zone
     * @return RemoteCommandOutput
     */
    public function deleteInstance(string $name, string $zone): RemoteCommandOutput
    {
        return $this->gcloudCommand('compute instances delete ' . self::escapeShellArgument($name)
            . ' --zone ' . self::escapeShellArgument($zone)
            . ' --quiet');
    }

    /**
     * Set the current project in gcloud if configured.
     *
     * @return RemoteCommandOutput
     */
    public function applyProject(): RemoteCommandOutput
    {
        if ($this->projectId === null) {
            throw new \RuntimeException('GCP project ID is not configured. Call setProject() first.');
        }

        return $this->gcloudCommand('config set project ' . self::escapeShellArgument($this->projectId));
    }
}
