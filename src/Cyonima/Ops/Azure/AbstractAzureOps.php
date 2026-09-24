<?php

declare(strict_types=1);

namespace Cyonima\Ops\Azure;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for Azure operations via Azure CLI over SSH
 *
 * Requires the Azure CLI (`az`) to be installed and available in the remote
 * environment's PATH.
 */
abstract class AbstractAzureOps extends AbstractOps
{
    /**
     * Execute an Azure CLI subcommand.
     *
     * @param string $subcommand Azure CLI subcommand (without leading `az`)
     * @return RemoteCommandOutput
     */
    protected function azCommand(string $subcommand): RemoteCommandOutput
    {
        return $this->remoteExec('az ' . $subcommand);
    }

    /**
     * Get installed Azure CLI version.
     *
     * @return RemoteCommandOutput
     */
    public function getAzureCliVersion(): RemoteCommandOutput
    {
        return $this->azCommand('version --output json');
    }

    /**
     * Login using a service principal.
     *
     * @param string $tenantId
     * @param string $clientId
     * @param string $clientSecret
     * @return RemoteCommandOutput
     */
    public function loginWithServicePrincipal(string $tenantId, string $clientId, string $clientSecret): RemoteCommandOutput
    {
        $tenantId = self::escapeShellArgument($tenantId);
        $clientId = self::escapeShellArgument($clientId);
        $clientSecret = self::escapeShellArgument($clientSecret);

        return $this->azCommand('login --service-principal --username ' . $clientId .
            ' --password ' . $clientSecret .
            ' --tenant ' . $tenantId .
            ' --output json');
    }

    /**
     * Logout from the current Azure CLI session.
     *
     * @return RemoteCommandOutput
     */
    public function logout(): RemoteCommandOutput
    {
        return $this->azCommand('logout');
    }

    /**
     * List available Azure subscriptions.
     *
     * @return RemoteCommandOutput
     */
    public function listSubscriptions(): RemoteCommandOutput
    {
        return $this->azCommand('account list --output json');
    }

    /**
     * Set the active Azure subscription.
     *
     * @param string $subscriptionId
     * @return RemoteCommandOutput
     */
    public function setSubscription(string $subscriptionId): RemoteCommandOutput
    {
        return $this->azCommand('account set --subscription ' . self::escapeShellArgument($subscriptionId));
    }

    /**
     * List resource groups.
     *
     * @return RemoteCommandOutput
     */
    public function listResourceGroups(): RemoteCommandOutput
    {
        return $this->azCommand('group list --output json');
    }

    /**
     * Create a resource group.
     *
     * @param string $name
     * @param string $location
     * @return RemoteCommandOutput
     */
    public function createResourceGroup(string $name, string $location): RemoteCommandOutput
    {
        return $this->azCommand('group create --name ' . self::escapeShellArgument($name) .
            ' --location ' . self::escapeShellArgument($location) .
            ' --output json');
    }

    /**
     * Delete a resource group.
     *
     * @param string $name
     * @return RemoteCommandOutput
     */
    public function deleteResourceGroup(string $name): RemoteCommandOutput
    {
        return $this->azCommand('group delete --name ' . self::escapeShellArgument($name) . ' --yes --no-wait');
    }

    /**
     * List virtual machines in Azure.
     *
     * @param string|null $resourceGroup Optional resource group filter
     * @return RemoteCommandOutput
     */
    public function listVirtualMachines(?string $resourceGroup = null): RemoteCommandOutput
    {
        $command = 'vm list --show-details --output json';
        if ($resourceGroup !== null && trim($resourceGroup) !== '') {
            $command .= ' --resource-group ' . self::escapeShellArgument($resourceGroup);
        }

        return $this->azCommand($command);
    }

    /**
     * Create a virtual machine.
     *
     * @param string $name
     * @param string $resourceGroup
     * @param string $image
     * @param string $vmSize
     * @param string $adminUsername
     * @param string|null $adminPassword If null, SSH keys are generated
     * @return RemoteCommandOutput
     */
    public function createVirtualMachine(
        string $name,
        string $resourceGroup,
        string $image = 'UbuntuLTS',
        string $vmSize = 'Standard_B1s',
        string $adminUsername = 'azureuser',
        ?string $adminPassword = null
    ): RemoteCommandOutput {
        $command = 'vm create --name ' . self::escapeShellArgument($name) .
            ' --resource-group ' . self::escapeShellArgument($resourceGroup) .
            ' --image ' . self::escapeShellArgument($image) .
            ' --size ' . self::escapeShellArgument($vmSize) .
            ' --admin-username ' . self::escapeShellArgument($adminUsername);

        if ($adminPassword !== null && trim($adminPassword) !== '') {
            $command .= ' --authentication-type password' .
                ' --admin-password ' . self::escapeShellArgument($adminPassword);
        } else {
            $command .= ' --generate-ssh-keys';
        }

        $command .= ' --output json';
        return $this->azCommand($command);
    }

    /**
     * Start a virtual machine.
     *
     * @param string $name
     * @param string $resourceGroup
     * @return RemoteCommandOutput
     */
    public function startVirtualMachine(string $name, string $resourceGroup): RemoteCommandOutput
    {
        return $this->azCommand('vm start --name ' . self::escapeShellArgument($name) .
            ' --resource-group ' . self::escapeShellArgument($resourceGroup));
    }

    /**
     * Stop a virtual machine.
     *
     * @param string $name
     * @param string $resourceGroup
     * @return RemoteCommandOutput
     */
    public function stopVirtualMachine(string $name, string $resourceGroup): RemoteCommandOutput
    {
        return $this->azCommand('vm stop --name ' . self::escapeShellArgument($name) .
            ' --resource-group ' . self::escapeShellArgument($resourceGroup));
    }

    /**
     * Delete a virtual machine.
     *
     * @param string $name
     * @param string $resourceGroup
     * @return RemoteCommandOutput
     */
    public function deleteVirtualMachine(string $name, string $resourceGroup): RemoteCommandOutput
    {
        return $this->azCommand('vm delete --name ' . self::escapeShellArgument($name) .
            ' --resource-group ' . self::escapeShellArgument($resourceGroup) .
            ' --yes --no-wait');
    }
}
