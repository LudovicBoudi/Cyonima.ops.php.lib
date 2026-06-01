<?php

declare(strict_types=1);

namespace Cyonima\Ops\Kubernetes;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Kubernetes operations helper class
 *
 * Provides a direct interface to manage Kubernetes clusters, pods, services,
 * deployments, and Helm releases via kubectl and helm CLI over SSH connections.
 *
 * Example usage:
 * ```php
 * $k8s = new KubernetesOps();
 * $k8s->setHost('k8s-controller.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $k8s->openConnection();
 * $k8s->listPods();
 * $k8s->getClusterInfo();
 * $k8s->closeConnection();
 * ```
 */
class KubernetesOps extends AbstractOps
{
    private ?string $kubeconfig = null;

    /**
     * Set a custom kubeconfig file path
     *
     * All subsequent kubectl and helm commands will use this kubeconfig.
     *
     * @param string $path Absolute path to the kubeconfig file
     * @return self
     */
    public function setKubeconfig(string $path): self
    {
        $this->kubeconfig = $path;

        return $this;
    }

    /**
     * Build the kubectl command prefix, optionally with KUBECONFIG env var
     *
     * @return string
     */
    private function kubectlPrefix(): string
    {
        if ($this->kubeconfig !== null) {
            return 'KUBECONFIG=' . self::escapeShellArgument($this->kubeconfig) . ' kubectl';
        }

        return 'kubectl';
    }

    /**
     * Build the helm command prefix, optionally with --kubeconfig flag
     *
     * @return string
     */
    private function helmPrefix(): string
    {
        $cmd = 'helm';
        if ($this->kubeconfig !== null) {
            $cmd .= ' --kubeconfig ' . self::escapeShellArgument($this->kubeconfig);
        }

        return $cmd;
    }

    /**
     * Get the kubectl client version
     *
     * @return RemoteCommandOutput
     */
    public function getVersion(): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . " version --short --client");
    }

    /**
     * List all pods in a namespace
     *
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function listPods(string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' get pods -o wide -n ' . self::escapeShellArgument($namespace));
    }

    /**
     * List all services in a namespace
     *
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function listServices(string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' get svc -o wide -n ' . self::escapeShellArgument($namespace));
    }

    /**
     * List all deployments in a namespace
     *
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function listDeployments(string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' get deployments -o wide -n ' . self::escapeShellArgument($namespace));
    }

    /**
     * List all nodes in the cluster
     *
     * @return RemoteCommandOutput
     */
    public function listNodes(): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' get nodes -o wide');
    }

    /**
     * List all namespaces
     *
     * @return RemoteCommandOutput
     */
    public function listNamespaces(): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' get namespaces');
    }

    /**
     * Get logs from a specific pod
     *
     * @param string $pod Pod name
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @param int $lines Number of lines to retrieve from the tail (default: 50)
     * @return RemoteCommandOutput
     */
    public function getPodLogs(string $pod, string $namespace = 'default', int $lines = 50): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' logs --tail=' . $lines . ' -n ' . self::escapeShellArgument($namespace) . ' ' . self::escapeShellArgument($pod));
    }

    /**
     * Execute a command inside a running pod
     *
     * @param string $pod Pod name
     * @param string $command Command to execute inside the pod
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function execInPod(string $pod, string $command, string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' exec -n ' . self::escapeShellArgument($namespace) . ' ' . self::escapeShellArgument($pod) . ' -- ' . self::escapeShellArgument($command));
    }

    /**
     * Apply a Kubernetes manifest from a file
     *
     * @param string $file Path to the manifest file on the remote host
     * @return RemoteCommandOutput
     */
    public function applyManifest(string $file): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' apply -f ' . self::escapeShellArgument($file));
    }

    /**
     * Delete resources defined in a manifest file
     *
     * @param string $file Path to the manifest file on the remote host
     * @return RemoteCommandOutput
     */
    public function deleteManifest(string $file): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' delete -f ' . self::escapeShellArgument($file));
    }

    /**
     * Apply a Kubernetes manifest from raw YAML content
     *
     * The YAML content is base64-encoded to avoid shell escaping issues
     * and piped into kubectl apply via stdin.
     *
     * @param string $yamlContent Raw YAML manifest content
     * @return RemoteCommandOutput
     */
    public function applyManifestContent(string $yamlContent): RemoteCommandOutput
    {
        $base64 = base64_encode($yamlContent);

        return $this->remoteExec('echo ' . self::escapeShellArgument($base64) . ' | base64 --decode | ' . $this->kubectlPrefix() . ' apply -f -');
    }

    /**
     * Scale a deployment to a specific number of replicas
     *
     * @param string $name Deployment name
     * @param int $replicas Number of desired replicas
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function scaleDeployment(string $name, int $replicas, string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' scale deployment/' . self::escapeShellArgument($name) . ' --replicas=' . $replicas . ' -n ' . self::escapeShellArgument($namespace));
    }

    /**
     * Check the rollout status of a deployment
     *
     * @param string $name Deployment name
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function rolloutStatus(string $name, string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' rollout status deployment/' . self::escapeShellArgument($name) . ' -n ' . self::escapeShellArgument($namespace));
    }

    /**
     * Rollback a deployment to the previous revision
     *
     * @param string $name Deployment name
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function rolloutUndo(string $name, string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' rollout undo deployment/' . self::escapeShellArgument($name) . ' -n ' . self::escapeShellArgument($namespace));
    }

    /**
     * Get events sorted by timestamp from a namespace
     *
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function getEvents(string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . " get events --sort-by='.lastTimestamp' -n " . self::escapeShellArgument($namespace));
    }

    /**
     * Describe a specific pod
     *
     * @param string $pod Pod name
     * @param string $namespace Kubernetes namespace (default: 'default')
     * @return RemoteCommandOutput
     */
    public function describePod(string $pod, string $namespace = 'default'): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' describe pod ' . self::escapeShellArgument($pod) . ' -n ' . self::escapeShellArgument($namespace));
    }

    /**
     * Get cluster info
     *
     * @return RemoteCommandOutput
     */
    public function getClusterInfo(): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' cluster-info');
    }

    /**
     * List available API resources
     *
     * @return RemoteCommandOutput
     */
    public function getApiResources(): RemoteCommandOutput
    {
        return $this->remoteExec($this->kubectlPrefix() . ' api-resources');
    }

    /**
     * List Helm releases
     *
     * @param string|null $namespace Kubernetes namespace (optional)
     * @return RemoteCommandOutput
     */
    public function helmList(?string $namespace = null): RemoteCommandOutput
    {
        $command = $this->helmPrefix() . ' list';
        if ($namespace !== null) {
            $command .= ' --namespace ' . self::escapeShellArgument($namespace);
        }

        return $this->remoteExec($command);
    }

    /**
     * Install a Helm chart
     *
     * @param string $release Release name
     * @param string $chart Chart reference (e.g. 'stable/nginx')
     * @param string|null $namespace Kubernetes namespace (optional)
     * @return RemoteCommandOutput
     */
    public function helmInstall(string $release, string $chart, ?string $namespace = null): RemoteCommandOutput
    {
        $command = $this->helmPrefix() . ' install ' . self::escapeShellArgument($release) . ' ' . self::escapeShellArgument($chart);
        if ($namespace !== null) {
            $command .= ' --namespace ' . self::escapeShellArgument($namespace);
        }

        return $this->remoteExec($command);
    }

    /**
     * Uninstall a Helm release
     *
     * @param string $release Release name
     * @param string|null $namespace Kubernetes namespace (optional)
     * @return RemoteCommandOutput
     */
    public function helmUninstall(string $release, ?string $namespace = null): RemoteCommandOutput
    {
        $command = $this->helmPrefix() . ' uninstall ' . self::escapeShellArgument($release);
        if ($namespace !== null) {
            $command .= ' --namespace ' . self::escapeShellArgument($namespace);
        }

        return $this->remoteExec($command);
    }

    /**
     * Upgrade a Helm release
     *
     * @param string $release Release name
     * @param string $chart Chart reference (e.g. 'stable/nginx')
     * @param string|null $namespace Kubernetes namespace (optional)
     * @return RemoteCommandOutput
     */
    public function helmUpgrade(string $release, string $chart, ?string $namespace = null): RemoteCommandOutput
    {
        $command = $this->helmPrefix() . ' upgrade ' . self::escapeShellArgument($release) . ' ' . self::escapeShellArgument($chart);
        if ($namespace !== null) {
            $command .= ' --namespace ' . self::escapeShellArgument($namespace);
        }

        return $this->remoteExec($command);
    }

    /**
     * Update Helm repository cache
     *
     * @return RemoteCommandOutput
     */
    public function helmRepoUpdate(): RemoteCommandOutput
    {
        return $this->remoteExec($this->helmPrefix() . ' repo update');
    }
}
