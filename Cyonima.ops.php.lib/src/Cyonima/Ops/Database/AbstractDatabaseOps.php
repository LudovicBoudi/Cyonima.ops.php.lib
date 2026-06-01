<?php

declare(strict_types=1);

namespace Cyonima\Ops\Database;

use Cyonima\Ops\AbstractOps;
use Cyonima\Ops\RemoteCommandOutput;

/**
 * Abstract base class for database operations
 *
 * Provides a common interface for executing database commands such as
 * queries, dumps, restores, and user management across different database engines.
 *
 * Extending classes must implement the abstract methods using the appropriate
 * CLI tools for the specific database system (e.g., mysql, psql, pg_dump).
 */
abstract class AbstractDatabaseOps extends AbstractOps
{
    /**
     * Execute a SQL query against the specified database.
     *
     * @param string $database The database name
     * @param string $query    The SQL query to execute
     * @return RemoteCommandOutput
     */
    abstract public function query(string $database, string $query): RemoteCommandOutput;

    /**
     * Dump the specified database to a file.
     *
     * @param string $database   The database name
     * @param string $outputFile Path to the output dump file
     * @return RemoteCommandOutput
     */
    abstract public function dump(string $database, string $outputFile): RemoteCommandOutput;

    /**
     * Restore the specified database from a dump file.
     *
     * @param string $database  The database name
     * @param string $inputFile Path to the input dump file
     * @return RemoteCommandOutput
     */
    abstract public function restore(string $database, string $inputFile): RemoteCommandOutput;

    /**
     * Import a SQL file into the specified database.
     *
     * @param string $database The database name
     * @param string $sqlFile  Path to the SQL file to import
     * @return RemoteCommandOutput
     */
    abstract public function importSqlFile(string $database, string $sqlFile): RemoteCommandOutput;

    /**
     * List all databases on the server.
     *
     * @return RemoteCommandOutput
     */
    abstract public function listDatabases(): RemoteCommandOutput;

    /**
     * List all tables in the specified database.
     *
     * @param string $database The database name
     * @return RemoteCommandOutput
     */
    abstract public function listTables(string $database): RemoteCommandOutput;

    /**
     * Create a new database with the given name.
     *
     * @param string $name The database name
     * @return RemoteCommandOutput
     */
    abstract public function createDatabase(string $name): RemoteCommandOutput;

    /**
     * Drop (delete) a database by name.
     *
     * @param string $name The database name
     * @return RemoteCommandOutput
     */
    abstract public function dropDatabase(string $name): RemoteCommandOutput;

    /**
     * Create a new database user with the given credentials.
     *
     * @param string $username The username
     * @param string $password The password
     * @return RemoteCommandOutput
     */
    abstract public function createUser(string $username, string $password): RemoteCommandOutput;

    /**
     * Drop (delete) a database user.
     *
     * @param string $username The username
     * @return RemoteCommandOutput
     */
    abstract public function dropUser(string $username): RemoteCommandOutput;

    /**
     * Grant all privileges on a database to a user.
     *
     * @param string $username The username
     * @param string $database The database name
     * @return RemoteCommandOutput
     */
    abstract public function grantAllPrivileges(string $username, string $database): RemoteCommandOutput;

    /**
     * Get the database server version.
     *
     * @return RemoteCommandOutput
     */
    abstract public function serverVersion(): RemoteCommandOutput;

    /**
     * Get the database server status.
     *
     * @return RemoteCommandOutput
     */
    abstract public function serverStatus(): RemoteCommandOutput;

    /**
     * Build connection arguments for the database CLI tool.
     *
     * Override in subclasses to provide engine-specific connection parameters.
     *
     * @return string
     */
    protected function buildConnectionArgs(): string
    {
        return '';
    }

    /**
     * Build MySQL client arguments using a defaults file.
     *
     * @param string $defaultsFile Path to the MySQL defaults file (default: /etc/mysql/debian.cnf)
     * @return string
     */
    protected function buildMysqlClientArgs(string $defaultsFile = '/etc/mysql/debian.cnf'): string
    {
        return ' --defaults-file=' . self::escapeShellArgument($defaultsFile);
    }
}
