<?php

declare(strict_types=1);

namespace Cyonima\Ops\Database;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * PostgreSQL database operations via the psql CLI over SSH
 *
 * Provides direct access to PostgreSQL databases using the psql, pg_dump,
 * and pg_restore command-line tools executed over an SSH connection.
 * Uses PGPASSWORD environment variable for password-based authentication.
 *
 * Example usage:
 * ```php
 * $pg = new PostgreSqlOps();
 * $pg->setHost('pg-server.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $pg->openConnection();
 * $pg->setConnection('localhost', 5432, 'admin', 'secret');
 * $result = $pg->query('mydb', 'SELECT * FROM users');
 * $pg->closeConnection();
 * ```
 */
class PostgreSqlOps extends AbstractDatabaseOps
{
    private string $pgHost = 'localhost';
    private int $pgPort = 5432;
    private ?string $pgUser = null;
    private ?string $pgPassword = null;

    /**
     * Set the PostgreSQL server connection parameters.
     *
     * @param string $host     PostgreSQL server hostname or IP
     * @param int    $port     PostgreSQL server port
     * @param string $user     PostgreSQL username
     * @param string $password PostgreSQL password
     * @return self
     */
    public function setConnection(string $host, int $port, string $user, string $password): self
    {
        $this->pgHost = $host;
        $this->pgPort = $port;
        $this->pgUser = $user;
        $this->pgPassword = $password;
        return $this;
    }

    /**
     * Build the PGPASSWORD environment variable prefix for commands.
     *
     * @return string
     */
    private function buildPostgresEnv(): string
    {
        if ($this->pgPassword !== null) {
            return 'PGPASSWORD=' . self::escapeShellArgument($this->pgPassword) . ' ';
        }
        return '';
    }

    /**
     * Build PostgreSQL CLI connection arguments.
     *
     * @return string
     */
    protected function buildConnectionArgs(): string
    {
        $args = '';
        if ($this->pgHost !== 'localhost') {
            $args .= ' -h ' . self::escapeShellArgument($this->pgHost);
        }
        if ($this->pgPort !== 5432) {
            $args .= ' -p ' . self::escapeShellArgument((string)$this->pgPort);
        }
        if ($this->pgUser !== null) {
            $args .= ' -U ' . self::escapeShellArgument($this->pgUser);
        }
        return $args;
    }

    /**
     * Execute a SQL query against the specified database.
     *
     * @param string $database The database name
     * @param string $query    The SQL query to execute
     * @return RemoteCommandOutput
     */
    public function query(string $database, string $query): RemoteCommandOutput
    {
        $database = self::escapeShellArgument($database);
        $query = self::escapeShellArgument($query);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -d ' . $database . ' -t -A -c ' . $query);
    }

    /**
     * Dump the specified database to a file using pg_dump.
     *
     * @param string $database   The database name
     * @param string $outputFile Path to the output dump file
     * @return RemoteCommandOutput
     */
    public function dump(string $database, string $outputFile): RemoteCommandOutput
    {
        $database = self::escapeShellArgument($database);
        $outputFile = self::escapeShellArgument($outputFile);
        return $this->remoteExec($this->buildPostgresEnv() . 'pg_dump' . $this->buildConnectionArgs() . ' -d ' . $database . ' -F c -f ' . $outputFile);
    }

    /**
     * Restore the specified database from a dump file using pg_restore.
     *
     * @param string $database  The database name
     * @param string $inputFile Path to the input dump file
     * @return RemoteCommandOutput
     */
    public function restore(string $database, string $inputFile): RemoteCommandOutput
    {
        $database = self::escapeShellArgument($database);
        $inputFile = self::escapeShellArgument($inputFile);
        return $this->remoteExec($this->buildPostgresEnv() . 'pg_restore' . $this->buildConnectionArgs() . ' -d ' . $database . ' -c ' . $inputFile);
    }

    /**
     * Import a SQL file into the specified database.
     *
     * @param string $database The database name
     * @param string $sqlFile  Path to the SQL file to import
     * @return RemoteCommandOutput
     */
    public function importSqlFile(string $database, string $sqlFile): RemoteCommandOutput
    {
        $database = self::escapeShellArgument($database);
        $sqlFile = self::escapeShellArgument($sqlFile);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -d ' . $database . ' -f ' . $sqlFile);
    }

    /**
     * List all databases on the PostgreSQL server.
     *
     * @return RemoteCommandOutput
     */
    public function listDatabases(): RemoteCommandOutput
    {
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -l -t -A');
    }

    /**
     * List all tables in the specified database.
     *
     * @param string $database The database name
     * @return RemoteCommandOutput
     */
    public function listTables(string $database): RemoteCommandOutput
    {
        $database = self::escapeShellArgument($database);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -d ' . $database . ' -c "\dt" -t -A');
    }

    /**
     * Create a new database with the given name.
     *
     * @param string $name The database name
     * @return RemoteCommandOutput
     */
    public function createDatabase(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -c "CREATE DATABASE ' . $name . '"');
    }

    /**
     * Drop (delete) a database by name.
     *
     * @param string $name The database name
     * @return RemoteCommandOutput
     */
    public function dropDatabase(string $name): RemoteCommandOutput
    {
        $name = self::escapeShellArgument($name);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -c "DROP DATABASE IF EXISTS ' . $name . '"');
    }

    /**
     * Create a new PostgreSQL user.
     *
     * @param string $username The username
     * @param string $password The password
     * @return RemoteCommandOutput
     */
    public function createUser(string $username, string $password): RemoteCommandOutput
    {
        $username = self::escapeShellArgument($username);
        $password = self::escapeShellArgument($password);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -c "CREATE USER ' . $username . ' WITH PASSWORD ' . $password . '"');
    }

    /**
     * Drop (delete) a PostgreSQL user.
     *
     * @param string $username The username
     * @return RemoteCommandOutput
     */
    public function dropUser(string $username): RemoteCommandOutput
    {
        $username = self::escapeShellArgument($username);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -c "DROP USER IF EXISTS ' . $username . '"');
    }

    /**
     * Grant all privileges on a database to a user.
     *
     * @param string $username The username
     * @param string $database The database name
     * @return RemoteCommandOutput
     */
    public function grantAllPrivileges(string $username, string $database): RemoteCommandOutput
    {
        $username = self::escapeShellArgument($username);
        $database = self::escapeShellArgument($database);
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -c "GRANT ALL PRIVILEGES ON DATABASE ' . $database . ' TO ' . $username . '"');
    }

    /**
     * Get the PostgreSQL server version (local client).
     *
     * @return RemoteCommandOutput
     */
    public function serverVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('psql --version');
    }

    /**
     * Get the PostgreSQL server status by querying the version.
     *
     * @return RemoteCommandOutput
     */
    public function serverStatus(): RemoteCommandOutput
    {
        return $this->remoteExec($this->buildPostgresEnv() . 'psql' . $this->buildConnectionArgs() . ' -c "SELECT version();"');
    }
}
