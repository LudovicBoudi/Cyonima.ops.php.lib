<?php

declare(strict_types=1);

namespace Cyonima\Ops\Database;

use Cyonima\Ops\RemoteCommandOutput;

/**
 * MySQL database operations via the mysql CLI over SSH
 *
 * Provides direct access to MySQL databases using the mysql, mysqldump,
 * and mysqladmin command-line tools executed over an SSH connection.
 *
 * Example usage:
 * ```php
 * $mysql = new MySqlOps();
 * $mysql->setHost('db-server.example.local')
 *     ->setCredentials('root', 'password')
 *     ->setSshPort(22);
 * $mysql->openConnection();
 * $mysql->setConnection('localhost', 3306, 'admin', 'secret');
 * $result = $mysql->query('mydb', 'SELECT * FROM users');
 * $mysql->closeConnection();
 * ```
 */
class MySqlOps extends AbstractDatabaseOps
{
    private string $mysqlHost = 'localhost';
    private int $mysqlPort = 3306;
    private ?string $mysqlUser = null;
    private ?string $mysqlPassword = null;

    /**
     * Set the MySQL server connection parameters.
     *
     * @param string $host     MySQL server hostname or IP
     * @param int    $port     MySQL server port
     * @param string $user     MySQL username
     * @param string $password MySQL password
     * @return self
     */
    public function setConnection(string $host, int $port, string $user, string $password): self
    {
        $this->mysqlHost = $host;
        $this->mysqlPort = $port;
        $this->mysqlUser = $user;
        $this->mysqlPassword = $password;
        return $this;
    }

    /**
     * Build MySQL CLI connection arguments.
     *
     * @return string
     */
    protected function buildConnectionArgs(): string
    {
        $args = '';
        if ($this->mysqlHost !== 'localhost') {
            $args .= ' --host=' . self::escapeShellArgument($this->mysqlHost);
        }
        if ($this->mysqlPort !== 3306) {
            $args .= ' --port=' . self::escapeShellArgument((string)$this->mysqlPort);
        }
        if ($this->mysqlUser !== null) {
            $args .= ' --user=' . self::escapeShellArgument($this->mysqlUser);
        }
        if ($this->mysqlPassword !== null) {
            $args .= ' --password=' . self::escapeShellArgument($this->mysqlPassword);
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
        return $this->remoteExec('mysql --batch --silent ' . $this->buildConnectionArgs() . ' ' . $database . ' -e ' . $query);
    }

    /**
     * Dump the specified database to a file using mysqldump.
     *
     * @param string $database   The database name
     * @param string $outputFile Path to the output dump file
     * @return RemoteCommandOutput
     */
    public function dump(string $database, string $outputFile): RemoteCommandOutput
    {
        $database = self::escapeShellArgument($database);
        $outputFile = self::escapeShellArgument($outputFile);
        return $this->remoteExec('mysqldump ' . $this->buildConnectionArgs() . ' ' . $database . ' > ' . $outputFile);
    }

    /**
     * Restore the specified database from a dump file.
     *
     * @param string $database  The database name
     * @param string $inputFile Path to the input dump file
     * @return RemoteCommandOutput
     */
    public function restore(string $database, string $inputFile): RemoteCommandOutput
    {
        $database = self::escapeShellArgument($database);
        $inputFile = self::escapeShellArgument($inputFile);
        return $this->remoteExec('mysql ' . $this->buildConnectionArgs() . ' ' . $database . ' < ' . $inputFile);
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
        return $this->remoteExec('mysql ' . $this->buildConnectionArgs() . ' ' . $database . ' < ' . $sqlFile);
    }

    /**
     * List all databases on the MySQL server.
     *
     * @return RemoteCommandOutput
     */
    public function listDatabases(): RemoteCommandOutput
    {
        return $this->remoteExec('mysql --batch --silent ' . $this->buildConnectionArgs() . " -e 'SHOW DATABASES'");
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
        return $this->remoteExec('mysql --batch --silent ' . $this->buildConnectionArgs() . ' ' . $database . " -e 'SHOW TABLES'");
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
        return $this->remoteExec('mysql ' . $this->buildConnectionArgs() . ' -e "CREATE DATABASE ' . $name . '"');
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
        return $this->remoteExec('mysql ' . $this->buildConnectionArgs() . ' -e "DROP DATABASE IF EXISTS ' . $name . '"');
    }

    /**
     * Create a new MySQL user.
     *
     * @param string $username The username
     * @param string $password The password
     * @return RemoteCommandOutput
     */
    public function createUser(string $username, string $password): RemoteCommandOutput
    {
        $username = self::escapeShellArgument($username);
        $password = self::escapeShellArgument($password);
        return $this->remoteExec('mysql ' . $this->buildConnectionArgs() . ' -e "CREATE USER ' . $username . "@'localhost' IDENTIFIED BY " . $password . '"');
    }

    /**
     * Drop (delete) a MySQL user.
     *
     * @param string $username The username
     * @return RemoteCommandOutput
     */
    public function dropUser(string $username): RemoteCommandOutput
    {
        $username = self::escapeShellArgument($username);
        return $this->remoteExec('mysql ' . $this->buildConnectionArgs() . ' -e "DROP USER IF EXISTS ' . $username . "@'localhost'\"");
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
        return $this->remoteExec('mysql ' . $this->buildConnectionArgs() . ' -e "GRANT ALL PRIVILEGES ON ' . $database . '.* TO ' . $username . "@'localhost'; FLUSH PRIVILEGES;\"");
    }

    /**
     * Get the MySQL server version.
     *
     * @return RemoteCommandOutput
     */
    public function serverVersion(): RemoteCommandOutput
    {
        return $this->remoteExec('mysql --version');
    }

    /**
     * Get the MySQL server status via mysqladmin.
     *
     * @return RemoteCommandOutput
     */
    public function serverStatus(): RemoteCommandOutput
    {
        return $this->remoteExec('mysqladmin status ' . $this->buildConnectionArgs());
    }
}
