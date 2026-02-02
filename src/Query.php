<?php

declare(strict_types=1);

/**
 * This file is part of the FluentPDO package.
 *
 * FluentPDO is a quick and light PHP library for rapid query building. It features a smart join builder, which automatically creates table joins.
 *
 * For more information see readme.md
 *
 * @link      https://github.com/VitexSoftware/fluentpdo
 * @author    Chris Bornhoft, start@env.ms
 * @copyright 2012-2020 envms - Chris Bornhoft, Marek Lichtner
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License, version 3.0
 *
 * (G) 2025-2026 Vítězslav Dvořák <info@vitexsoftware.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Envms\FluentPDO;

use Envms\FluentPDO\Queries\Delete;
use Envms\FluentPDO\Queries\Insert;
use Envms\FluentPDO\Queries\Select;
use Envms\FluentPDO\Queries\Update;
use PDO;

/**
 * FluentPDO Query Builder.
 *
 * FluentPDO is a quick and light PHP library for rapid query building.
 * It features a smart join builder, which automatically creates table joins.
 *
 * For more information see readme.md
 *
 * @see      https://github.com/VitexSoftware/fluentpdo
 *
 * @author    Chris Bornhoft, start@env.ms
 * @author    Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright 2012-2020 envms - Chris Bornhoft, Marek Lichtner
 * @copyright 2025-2026 VitexSoftware
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License, version 3.0
 */

/**
 * Main Query class for FluentPDO.
 *
 * @method debug(Queries\Base $param) Enable debug mode for query execution
 */
class Query
{
    /**
     * Debug mode setting.
     *
     * @var bool|callable Debug flag or callable function for custom debug handling
     */
    public mixed $debug = false;

    /**
     * Type conversion setting for read operations.
     *
     * @var bool Determines whether to convert types when fetching rows from Select
     */
    public bool $convertRead = false;

    /**
     * Type conversion setting for write operations.
     *
     * @var bool Determines whether to convert types within Base::buildParameters()
     */
    public bool $convertWrite = false;

    /**
     * Exception handling setting.
     *
     * @var bool If a query errors, this determines how to handle it
     */
    public bool $exceptionOnError = false;
    protected \PDO $pdo;
    protected Structure $structure;
    protected string $table;
    protected string $prefix;
    protected string $separator;

    /**
     * Query constructor.
     *
     * @param \PDO           $pdo       PDO database connection instance
     * @param null|Structure $structure Optional structure for table relationships
     */
    public function __construct(\PDO $pdo, ?Structure $structure = null)
    {
        $this->pdo = $pdo;

        // if exceptions are already activated in PDO, activate them in Fluent as well
        if ($this->pdo->getAttribute(\PDO::ATTR_ERRMODE) === \PDO::ERRMODE_EXCEPTION) {
            $this->throwExceptionOnError(true);
        }

        $this->structure = ($structure instanceof Structure) ? $structure : new Structure();
    }

    /**
     * Create SELECT query from $table.
     *
     * @param null|string $table      Database table name
     * @param null|int    $primaryKey Return one row by primary key
     *
     * @throws Exception When table name is invalid
     *
     * @return Select SELECT query builder instance
     */
    public function from(?string $table = null, ?int $primaryKey = null): Select
    {
        $this->setTableName($table);
        $tableName = $this->getFullTableName();

        $query = new Select($this, $tableName);

        if ($primaryKey !== null) {
            $tableTable = $query->getFromTable();
            $tableAlias = $query->getFromAlias();
            $primaryKeyName = $this->structure->getPrimaryKey($tableTable);
            $query = $query->where("{$tableAlias}.{$primaryKeyName}", $primaryKey);
        }

        return $query;
    }

    /**
     * Create INSERT INTO query.
     *
     * @param null|string $table  Database table name
     * @param array       $values Accepts one or multiple rows of data to insert
     *
     * @throws Exception When table name is invalid
     *
     * @return Insert INSERT query builder instance
     */
    public function insertInto(?string $table = null, array $values = []): Insert
    {
        $this->setTableName($table);
        $table = $this->getFullTableName();

        return new Insert($this, $table, $values);
    }

    /**
     * Create UPDATE query.
     *
     * @param null|string  $table      Database table name
     * @param array|string $set        Column-value pairs to update or SET clause string
     * @param null|int     $primaryKey Update only row with this primary key
     *
     * @throws Exception When table name is invalid
     *
     * @return Update UPDATE query builder instance
     */
    public function update(?string $table = null, $set = [], ?int $primaryKey = null): Update
    {
        $this->setTableName($table);
        $table = $this->getFullTableName();

        $query = new Update($this, $table);

        $query->set($set);

        if ($primaryKey) {
            $primaryKeyName = $this->getStructure()->getPrimaryKey($this->table);
            $query = $query->where($primaryKeyName, $primaryKey);
        }

        return $query;
    }

    /**
     * Create DELETE query.
     *
     * @param null|string $table      Database table name
     * @param null|int    $primaryKey Delete only row by primary key
     *
     * @throws Exception When table name is invalid
     *
     * @return Delete DELETE query builder instance
     */
    public function delete(?string $table = null, ?int $primaryKey = null): Delete
    {
        $this->setTableName($table);
        $table = $this->getFullTableName();

        $query = new Delete($this, $table);

        if ($primaryKey) {
            $primaryKeyName = $this->getStructure()->getPrimaryKey($this->table);
            $query = $query->where($primaryKeyName, $primaryKey);
        }

        return $query;
    }

    /**
     * Create DELETE FROM query.
     *
     * @param null|string $table      Database table name
     * @param null|int    $primaryKey Delete only row by primary key
     *
     * @throws Exception When table name is invalid
     *
     * @return Delete DELETE query builder instance
     */
    public function deleteFrom(?string $table = null, ?int $primaryKey = null): Delete
    {
        $args = \func_get_args();

        return \call_user_func_array([$this, 'delete'], $args);
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function getStructure(): Structure
    {
        return $this->structure;
    }

    /**
     * Closes the \PDO connection to the database.
     */
    public function close(): void
    {
        $this->pdo = null;
    }

    /**
     * Set table name comprised of prefix.separator.table.
     *
     * @throws Exception
     *
     * @return $this
     */
    public function setTableName(?string $table = '', string $prefix = '', string $separator = ''): self
    {
        if ($table !== null) {
            $this->prefix = $prefix;
            $this->separator = $separator;
            $this->table = $table;
        }

        if ($this->getFullTableName() === '') {
            throw new Exception('Table name cannot be empty');
        }

        return $this;
    }

    public function getFullTableName(): string
    {
        return $this->prefix.$this->separator.$this->table;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function throwExceptionOnError(bool $flag): void
    {
        $this->exceptionOnError = $flag;
    }

    public function convertTypes(bool $read, bool $write): void
    {
        $this->convertRead = $read;
        $this->convertWrite = $write;
    }

    public function convertReadTypes(bool $flag): void
    {
        $this->convertRead = $flag;
    }

    public function convertWriteTypes(bool $flag): void
    {
        $this->convertWrite = $flag;
    }
}
