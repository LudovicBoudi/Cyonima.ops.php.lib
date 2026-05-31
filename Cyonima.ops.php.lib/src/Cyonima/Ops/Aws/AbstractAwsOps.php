<?php

declare(strict_types=1);

namespace Cyonima\Ops\Aws;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for AWS operations via AWS CLI over SSH.
 *
 * Requires the AWS CLI (`aws`) to be installed and available in the remote
 * environment's PATH.
 */
abstract class AbstractAwsOps extends AbstractOps
{
    private ?string $profile = null;
    private ?string $region = null;

    /**
     * Set the AWS CLI profile name.
     *
     * @param string $profile
     * @return self
     */
    public function setProfile(string $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Set the AWS CLI region.
     *
     * @param string $region
     * @return self
     */
    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    /**
     * Execute an AWS CLI subcommand.
     *
     * @param string $subcommand
     * @return RemoteCommandOutput
     */
    protected function awsCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('aws ' . $this->buildCliOptions() . ' ' . $subcommand);
    }

    /**
     * Get installed AWS CLI version.
     *
     * @return RemoteCommandOutput
     */
    public function getAwsCliVersion(): RemoteCommandOutput
    {
        return $this->awsCommand('--version');
    }

    /**
     * Configure AWS credentials for the selected profile.
     *
     * @param string $accessKeyId
     * @param string $secretAccessKey
     * @param string|null $region
     * @param string|null $outputFormat
     * @return RemoteCommandOutput
     */
    public function configureCredentials(
        string $accessKeyId,
        string $secretAccessKey,
        ?string $region = null,
        ?string $outputFormat = 'json'
    ): RemoteCommandOutput {
        $profile = $this->profile ?? 'default';
        $cmd = 'aws configure set aws_access_key_id ' . self::escapeShellArgument($accessKeyId) . ' --profile ' . self::escapeShellArgument($profile);
        $cmd .= ' && aws configure set aws_secret_access_key ' . self::escapeShellArgument($secretAccessKey) . ' --profile ' . self::escapeShellArgument($profile);

        if ($region !== null && trim($region) !== '') {
            $cmd .= ' && aws configure set region ' . self::escapeShellArgument($region) . ' --profile ' . self::escapeShellArgument($profile);
        }

        if ($outputFormat !== null && trim($outputFormat) !== '') {
            $cmd .= ' && aws configure set output ' . self::escapeShellArgument($outputFormat) . ' --profile ' . self::escapeShellArgument($profile);
        }

        return $this->remoteExec($cmd);
    }

    /**
     * List available AWS regions.
     *
     * @return RemoteCommandOutput
     */
    public function listRegions(): RemoteCommandOutput
    {
        return $this->awsCommand('ec2 describe-regions --output json');
    }

    /**
     * List availability zones.
     *
     * @return RemoteCommandOutput
     */
    public function listZones(): RemoteCommandOutput
    {
        return $this->awsCommand('ec2 describe-availability-zones --output json');
    }

    /**
     * List EC2 instances.
     *
     * @param string|null $zone Optional availability zone filter
     * @return RemoteCommandOutput
     */
    public function listInstances(?string $zone = null): RemoteCommandOutput
    {
        $command = 'ec2 describe-instances --output json';
        if ($zone !== null && trim($zone) !== '') {
            $command .= ' --filters Name=availability-zone,Values=' . self::escapeShellArgument($zone);
        }

        return $this->awsCommand($command);
    }

    /**
     * Launch a new EC2 instance.
     *
     * @param string $name
     * @param string $imageId
     * @param string $instanceType
     * @param string $subnetId
     * @param string $keyName
     * @param string|null $securityGroupId
     * @return RemoteCommandOutput
     */
    public function createInstance(
        string $name,
        string $imageId,
        string $instanceType,
        string $subnetId,
        string $keyName,
        ?string $securityGroupId = null
    ): RemoteCommandOutput {
        $command = 'ec2 run-instances --image-id ' . self::escapeShellArgument($imageId)
            . ' --instance-type ' . self::escapeShellArgument($instanceType)
            . ' --subnet-id ' . self::escapeShellArgument($subnetId)
            . ' --key-name ' . self::escapeShellArgument($keyName)
            . ' --tag-specifications ResourceType=instance,Tags=[{Key=Name,Value=' . self::escapeShellArgument($name) . '}]'
            . ' --output json';

        if ($securityGroupId !== null && trim($securityGroupId) !== '') {
            $command .= ' --security-group-ids ' . self::escapeShellArgument($securityGroupId);
        }

        return $this->awsCommand($command);
    }

    /**
     * Start an EC2 instance.
     *
     * @param string $instanceId
     * @return RemoteCommandOutput
     */
    public function startInstance(string $instanceId): RemoteCommandOutput
    {
        return $this->awsCommand('ec2 start-instances --instance-ids ' . self::escapeShellArgument($instanceId) . ' --output json');
    }

    /**
     * Stop an EC2 instance.
     *
     * @param string $instanceId
     * @return RemoteCommandOutput
     */
    public function stopInstance(string $instanceId): RemoteCommandOutput
    {
        return $this->awsCommand('ec2 stop-instances --instance-ids ' . self::escapeShellArgument($instanceId) . ' --output json');
    }

    /**
     * Terminate an EC2 instance.
     *
     * @param string $instanceId
     * @return RemoteCommandOutput
     */
    public function terminateInstance(string $instanceId): RemoteCommandOutput
    {
        return $this->awsCommand('ec2 terminate-instances --instance-ids ' . self::escapeShellArgument($instanceId) . ' --output json');
    }

    /**
     * Build command line options for AWS CLI.
     *
     * @return string
     */
    private function buildCliOptions(): string
    {
        $options = [];

        if ($this->profile !== null && trim($this->profile) !== '') {
            $options[] = '--profile ' . self::escapeShellArgument($this->profile);
        }

        if ($this->region !== null && trim($this->region) !== '') {
            $options[] = '--region ' . self::escapeShellArgument($this->region);
        }

        return implode(' ', $options);
    }
}
