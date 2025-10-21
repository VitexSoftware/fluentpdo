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

use Envms\FluentPDO\Query;

/**
 * Class Json.
 */
class Json extends Common
{
    protected mixed $fromTable;

    protected mixed $fromAlias;

    protected bool $convertTypes = false;

    /**
     * Json constructor.
     */
    public function __construct(Query $fluent, string $table)
    {
        $clauses = [
            'SELECT' => ', ',
            'JOIN' => [$this, 'getClauseJoin'],
            'WHERE' => [$this, 'getClauseWhere'],
            'GROUP BY' => ',',
            'HAVING' => ' AND ',
            'ORDER BY' => ', ',
            'LIMIT' => null,
            'OFFSET' => null,
            "\n--" => "\n--",
        ];

        parent::__construct($fluent, $clauses);

        // initialize statements
        $tableParts = explode(' ', $table);
        $this->fromTable = reset($tableParts);
        $this->fromAlias = end($tableParts);

        $this->statements['SELECT'][] = '';
        $this->joins[] = $this->fromAlias;

        if (isset($fluent->convertTypes) && $fluent->convertTypes) {
            $this->convertTypes = true;
        }
    }
}
