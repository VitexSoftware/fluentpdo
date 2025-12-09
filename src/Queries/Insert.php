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

namespace Envms\FluentPDO\Queries;

use Envms\FluentPDO\Exception;
use Envms\FluentPDO\Literal;
use Envms\FluentPDO\Query;

/**
 * INSERT query builder.
 */
class Insert extends Base
{
    private array $columns = [];
    private array $firstValue = [];
    private bool $ignore = false;
    private bool $delayed = false;

    /**
     * InsertQuery constructor.
     *
     * @param string $table
     * @param mixed  $values
     *
     * @throws Exception
     */
    public function __construct(Query $fluent, $table, $values)
    {
        $clauses = [
            'INSERT INTO' => [$this, 'getClauseInsertInto'],
            'VALUES' => [$this, 'getClauseValues'],
            'ON DUPLICATE KEY UPDATE' => [$this, 'getClauseOnDuplicateKeyUpdate'],
        ];
        parent::__construct($fluent, $clauses);

        $this->statements['INSERT INTO'] = $table;
        $this->values($values);
    }

    /**
     * Force insert operation to fail silently.
     *
     * @return Insert
     */
    public function ignore()
    {
        $this->ignore = true;

        return $this;
    }

    /**
     * Force insert operation delay support.
     *
     * @return Insert
     */
    public function delayed()
    {
        $this->delayed = true;

        return $this;
    }

    /**
     * Add VALUES.
     *
     * @param mixed $values
     *
     * @throws Exception
     *
     * @return Insert
     */
    public function values($values)
    {
        if (!\is_array($values)) {
            throw new Exception('Param VALUES for INSERT query must be array');
        }

        $first = current($values);

        if (\is_string(key($values))) {
            // is one row array
            $this->addOneValue($values);
        } elseif (\is_array($first) && \is_string(key($first))) {
            // this is multi values
            foreach ($values as $oneValue) {
                $this->addOneValue($oneValue);
            }
        }

        return $this;
    }

    /**
     * Add ON DUPLICATE KEY UPDATE.
     *
     * @param array $values
     *
     * @return Insert
     */
    public function onDuplicateKeyUpdate($values)
    {
        $this->statements['ON DUPLICATE KEY UPDATE'] = array_merge(
            $this->statements['ON DUPLICATE KEY UPDATE'],
            $values,
        );

        return $this;
    }

    /**
     * Execute insert query.
     *
     * @param mixed $sequence
     *
     * @throws Exception
     *
     * @return bool|int - Last inserted primary key
     */
    public function execute($sequence = null)
    {
        $result = parent::execute();

        if ($result) {
            return $this->fluent->getPdo()->lastInsertId($sequence);
        }

        return false;
    }

    /**
     * @param null $sequence
     *
     * @throws Exception
     *
     * @return bool
     */
    public function executeWithoutId($sequence = null)
    {
        $result = parent::execute();

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getClauseInsertInto()
    {
        return 'INSERT'.($this->ignore ? ' IGNORE' : '').($this->delayed ? ' DELAYED' : '').' INTO '.$this->statements['INSERT INTO'];
    }

    /**
     * @return string
     */
    protected function getClauseValues()
    {
        $valuesArray = [];

        foreach ($this->statements['VALUES'] as $rows) {
            // literals should not be parametrized.
            // They are commonly used to call engine functions or literals.
            // Eg: NOW(), CURRENT_TIMESTAMP etc
            $placeholders = array_map([$this, 'parameterGetValue'], $rows);
            $valuesArray[] = '('.implode(', ', $placeholders).')';
        }

        $columns = implode(', ', $this->columns);
        $values = implode(', ', $valuesArray);

        return " ({$columns}) VALUES {$values}";
    }

    /**
     * @return string
     */
    protected function getClauseOnDuplicateKeyUpdate()
    {
        $result = [];

        foreach ($this->statements['ON DUPLICATE KEY UPDATE'] as $key => $value) {
            $result[] = "{$key} = ".$this->parameterGetValue($value);
        }

        return ' ON DUPLICATE KEY UPDATE '.implode(', ', $result);
    }

    /**
     * @param mixed $param
     *
     * @return string
     */
    protected function parameterGetValue($param)
    {
        return $param instanceof Literal ? (string) $param : '?';
    }

    /**
     * Removes all Literal instances from the argument
     * since they are not to be used as PDO parameters but rather injected directly into the query.
     *
     * @param mixed $statements
     *
     * @return array
     */
    protected function filterLiterals($statements)
    {
        $f = static function ($item) {
            return !$item instanceof Literal;
        };

        return array_map(static function ($item) use ($f) {
            if (\is_array($item)) {
                return array_filter($item, $f);
            }

            return $item;
        }, array_filter($statements, $f));
    }

    protected function buildParameters(): array
    {
        $this->parameters = array_merge(
            $this->filterLiterals($this->statements['VALUES']),
            $this->filterLiterals($this->statements['ON DUPLICATE KEY UPDATE']),
        );

        return parent::buildParameters();
    }

    /**
     * @param array $oneValue
     *
     * @throws Exception
     */
    private function addOneValue($oneValue): void
    {
        // check if all $keys are strings
        foreach ($oneValue as $key => $value) {
            if (!\is_string($key)) {
                throw new Exception('INSERT query: All keys of value array have to be strings.');
            }
        }

        if (!$this->firstValue) {
            $this->firstValue = $oneValue;
        }

        if (!$this->columns) {
            $this->columns = array_keys($oneValue);
        }

        if ($this->columns !== array_keys($oneValue)) {
            throw new Exception('INSERT query: All VALUES have to same keys (columns).');
        }

        $this->statements['VALUES'][] = $oneValue;
    }
}
