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
use Envms\FluentPDO\Query;

/**
 * DELETE query builder.
 *
 * @method Delete innerJoin(string $statement) add INNER JOIN to query
 *                                             ($statement can be 'table' name only or 'table:' means back reference)
 * @method Delete from(string $table)          add LIMIT to query
 * @method Delete limit(int $limit)            add LIMIT to query
 * @method Delete orderBy(string $column)      add ORDER BY to query
 */
class Delete extends Common
{
    private bool $ignore = false;

    /**
     * Delete constructor.
     */
    public function __construct(Query $fluent, string $table)
    {
        $clauses = [
            'DELETE FROM' => [$this, 'getClauseDeleteFrom'],
            'DELETE' => [$this, 'getClauseDelete'],
            'FROM' => null,
            'JOIN' => [$this, 'getClauseJoin'],
            'WHERE' => [$this, 'getClauseWhere'],
            'ORDER BY' => ', ',
            'LIMIT' => null,
        ];

        parent::__construct($fluent, $clauses);

        $this->statements['DELETE FROM'] = $table;
        $this->statements['DELETE'] = $table;
    }

    /**
     * Forces delete operation to fail silently.
     *
     * @return Delete
     */
    public function ignore()
    {
        $this->ignore = true;

        return $this;
    }

    /**
     * Execute DELETE query.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function execute()
    {
        if (empty($this->statements['WHERE'])) {
            throw new Exception('Delete queries must contain a WHERE clause to prevent unwanted data loss');
        }

        $result = parent::execute();

        if ($result) {
            return $result->rowCount();
        }

        return false;
    }

    /**
     * @throws Exception
     *
     * @return string
     */
    protected function buildQuery()
    {
        if ($this->statements['FROM']) {
            unset($this->clauses['DELETE FROM']);
        } else {
            unset($this->clauses['DELETE']);
        }

        return parent::buildQuery();
    }

    /**
     * @return string
     */
    protected function getClauseDelete()
    {
        return 'DELETE'.($this->ignore ? ' IGNORE' : '').' '.$this->statements['DELETE'];
    }

    /**
     * @return string
     */
    protected function getClauseDeleteFrom()
    {
        return 'DELETE'.($this->ignore ? ' IGNORE' : '').' FROM '.$this->statements['DELETE FROM'];
    }
}
