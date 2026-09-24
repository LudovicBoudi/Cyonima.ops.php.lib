<?php

declare(strict_types=1);

namespace Cyonima\Ops\Docker;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Docker operations helper class
 *
 * Provides a direct interface to manage Docker containers, images, compose stacks,
 * and related resources via the Docker CLI over SSH connections.
 *
 * Example usage:
 * ```php
 * $docker = new DockerOps();
 * $docker->setHost('docker-host.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $docker->openConnection();
 * $docker->listContainers(true);
 * $docker->pullImage('nginx:latest');
 * $docker->startContainer('web-app');
 * $docker->closeConnection();
 * ```
 */
class DockerOps extends AbstractOps
{
    public const int DEFAULT_TIMEOUT = 10;
    public const int DEFAULT_LOG_LINES = 50;
    public const int DEFAULT_COMPOSE_LOG_LINES = 50;

    /**
     * Get the installed Docker daemon version
     *
     * @return RemoteCommandOutput
     */
    public function getDockerVersion(): RemoteCommandOutput
    {
        return $this->remoteExec("docker version --format '{{.Server.Version}}'");
    }

    /**
     * List Docker containers
     *
     * @param bool $all Whether to include stopped containers
     * @return RemoteCommandOutput
     */
    public function listContainers(bool $all = false): RemoteCommandOutput
    {
        $command = 'docker ps';
        if ($all) {
            $command .= ' -a';
        }
        $command .= " --format '{{json .}}'";

        return $this->remoteExec($command);
    }

    /**
     * List locally available Docker images
     *
     * @return RemoteCommandOutput
     */
    public function listImages(): RemoteCommandOutput
    {
        return $this->remoteExec("docker images --format '{{json .}}'");
    }

    /**
     * Start a stopped container
     *
     * @param string $name Container name or ID
     * @return RemoteCommandOutput
     */
    public function startContainer(string $name): RemoteCommandOutput
    {
        return $this->remoteExec('docker start ' . self::escapeShellArgument($name));
    }

    /**
     * Stop a running container
     *
     * @param string $name Container name or ID
     * @param int $timeout Seconds to wait before killing
     * @return RemoteCommandOutput
     */
    public function stopContainer(string $name, int $timeout = self::DEFAULT_TIMEOUT): RemoteCommandOutput
    {
        return $this->remoteExec('docker stop -t ' . $timeout . ' ' . self::escapeShellArgument($name));
    }

    /**
     * Restart a container
     *
     * @param string $name Container name or ID
     * @param int $timeout Seconds to wait before killing
     * @return RemoteCommandOutput
     */
    public function restartContainer(string $name, int $timeout = self::DEFAULT_TIMEOUT): RemoteCommandOutput
    {
        return $this->remoteExec('docker restart -t ' . $timeout . ' ' . self::escapeShellArgument($name));
    }

    /**
     * Remove a container
     *
     * @param string $name Container name or ID
     * @param bool $force Force removal of running container
     * @return RemoteCommandOutput
     */
    public function removeContainer(string $name, bool $force = false): RemoteCommandOutput
    {
        $command = 'docker rm';
        if ($force) {
            $command .= ' -f';
        }
        $command .= ' ' . self::escapeShellArgument($name);

        return $this->remoteExec($command);
    }

    /**
     * Pull a Docker image from a registry
     *
     * @param string $image Image name (e.g. nginx:latest)
     * @return RemoteCommandOutput
     */
    public function pullImage(string $image): RemoteCommandOutput
    {
        return $this->remoteExec('docker pull ' . self::escapeShellArgument($image));
    }

    /**
     * Remove a Docker image
     *
     * @param string $image Image name or ID
     * @param bool $force Force removal of the image
     * @return RemoteCommandOutput
     */
    public function removeImage(string $image, bool $force = false): RemoteCommandOutput
    {
        $command = 'docker rmi';
        if ($force) {
            $command .= ' -f';
        }
        $command .= ' ' . self::escapeShellArgument($image);

        return $this->remoteExec($command);
    }

    /**
     * Execute a command inside a running container
     *
     * @param string $name Container name or ID
     * @param string $command Command to execute inside the container
     * @return RemoteCommandOutput
     */
    public function execInContainer(string $name, string $command): RemoteCommandOutput
    {
        return $this->remoteExec('docker exec ' . self::escapeShellArgument($name) . ' ' . self::escapeShellArgument($command));
    }

    /**
     * Fetch logs from a container
     *
     * @param string $name Container name or ID
     * @param int $lines Number of lines to retrieve from the tail
     * @return RemoteCommandOutput
     */
    public function containerLogs(string $name, int $lines = self::DEFAULT_LOG_LINES): RemoteCommandOutput
    {
        return $this->remoteExec('docker logs --tail ' . $lines . ' ' . self::escapeShellArgument($name));
    }

    /**
     * Check if a container exists and return its status
     *
     * @param string $name Container name or ID
     * @return RemoteCommandOutput
     */
    public function containerExists(string $name): RemoteCommandOutput
    {
        return $this->remoteExec("docker inspect " . self::escapeShellArgument($name) . " --format '{{.State.Status}}'");
    }

    /**
     * Start a Docker Compose stack
     *
     * @param string|null $file Path to the compose file
     * @param bool $detach Run containers in the background
     * @return RemoteCommandOutput
     */
    public function composeUp(?string $file = null, bool $detach = true): RemoteCommandOutput
    {
        $command = 'docker compose';
        if ($file !== null && trim($file) !== '') {
            $command .= ' -f ' . self::escapeShellArgument($file);
        }
        $command .= ' up';
        if ($detach) {
            $command .= ' -d';
        }

        return $this->remoteExec($command);
    }

    /**
     * Stop and remove a Docker Compose stack
     *
     * @param string|null $file Path to the compose file
     * @return RemoteCommandOutput
     */
    public function composeDown(?string $file = null): RemoteCommandOutput
    {
        $command = 'docker compose';
        if ($file !== null && trim($file) !== '') {
            $command .= ' -f ' . self::escapeShellArgument($file);
        }
        $command .= ' down';

        return $this->remoteExec($command);
    }

    /**
     * Fetch logs from a Docker Compose stack
     *
     * @param string|null $file Path to the compose file
     * @param int $lines Number of lines to retrieve from the tail
     * @return RemoteCommandOutput
     */
    public function composeLogs(?string $file = null, int $lines = self::DEFAULT_COMPOSE_LOG_LINES): RemoteCommandOutput
    {
        $command = 'docker compose';
        if ($file !== null && trim($file) !== '') {
            $command .= ' -f ' . self::escapeShellArgument($file);
        }
        $command .= ' logs --tail ' . $lines;

        return $this->remoteExec($command);
    }

    /**
     * Get real-time container resource usage statistics
     *
     * @return RemoteCommandOutput
     */
    public function getContainerStats(): RemoteCommandOutput
    {
        return $this->remoteExec("docker stats --no-stream --format '{{json .}}'");
    }

    /**
     * Remove all stopped containers
     *
     * @return RemoteCommandOutput
     */
    public function pruneContainers(): RemoteCommandOutput
    {
        return $this->remoteExec('docker container prune -f');
    }

    /**
     * Remove all unused images
     *
     * @return RemoteCommandOutput
     */
    public function pruneImages(): RemoteCommandOutput
    {
        return $this->remoteExec('docker image prune -af');
    }

    /**
     * List Docker networks
     *
     * @return RemoteCommandOutput
     */
    public function networkList(): RemoteCommandOutput
    {
        return $this->remoteExec("docker network ls --format '{{json .}}'");
    }

    /**
     * List Docker volumes
     *
     * @return RemoteCommandOutput
     */
    public function volumeList(): RemoteCommandOutput
    {
        return $this->remoteExec("docker volume ls --format '{{json .}}'");
    }
}
