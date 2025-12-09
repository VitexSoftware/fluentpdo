<?php

declare(strict_types=1);

/**
 * This file is part of the EaseCore package.
 *
 * (c) Vítězslav Dvořák <info@vitexsoftware.cz>
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
 * FluentPDO is a quick and light PHP library for rapid query building. It features a smart join builder, which automatically creates table joins.
 *
 * For more information see readme.md
 *
 * @see      https://github.com/envms/fluentpdo
 *
 * @author    Chris Bornhoft, start@env.ms
 * @copyright 2012-2020 envms - Chris Bornhoft, Marek Lichtner
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License, version 3.0
 */

/**
 * Class Query.
 *
 * @method debug(Queries\Base $param)
 */
class Query
{
    /**
     * @var bool|callable
     */
    public mixed $debug = false;

    /**
     * @var bool - Determines whether to convert types when fetching rows from Select
     */
    public bool $convertRead = false;

    /**
     * @var bool - Determines whether to convert types within Base::buildParameters()
     */
    public bool $convertWrite = false;

    /**
     * @var bool - If a query errors, this determines how to handle it
     */
    public bool $exceptionOnError = false;
    protected \PDO $pdo;
    protected Structure $structure;
    protected string $table;
    protected string $prefix;
    protected string $separator;

    /**
     * Query constructor.
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
     * @param ?string $table      - db table name
     * @param ?int    $primaryKey - return one row by primary key
     *
     * @throws Exception
     */
    public function from(?string $table = null, ?int $primaryKey = null): Select
    {
        $this->setTableName($table);
        $table = $this->getFullTableName();

        $query = new Select($this, $table);

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
     * @param array $values - accepts one or multiple rows, @see docs
     *
     * @throws Exception
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
     * @param array|string $set
     *
     * @throws Exception
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
     * @param ?int $primaryKey delete only row by primary key
     *
     * @throws Exception
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
