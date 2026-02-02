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

/**
 * Database structure definition class.
 *
 * Handles primary key and foreign key conventions for automatic joins.
 *
 * @author Chris Bornhoft, start@env.ms
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class Structure
{
    private string $primaryKey;
    private string $foreignKey;

    /**
     * Structure constructor.
     *
     * @param string $primaryKey Primary key column name pattern (default: 'id')
     * @param string $foreignKey Foreign key column name pattern (default: '%s_id')
     */
    public function __construct(string $primaryKey = 'id', string $foreignKey = '%s_id')
    {
        if ($foreignKey === null) {
            $foreignKey = $primaryKey;
        }

        $this->primaryKey = $primaryKey;
        $this->foreignKey = $foreignKey;
    }

    /**
     * Get the primary key column name for a table.
     *
     * @param string $table The table name
     *
     * @return string The primary key column name
     */
    public function getPrimaryKey(string $table): string
    {
        return self::key($this->primaryKey, $table);
    }

    public function getForeignKey(string $table): string
    {
        return self::key($this->foreignKey, $table);
    }

    private static function key(callable|string $key, string $table): string
    {
        if (\is_callable($key)) {
            return $key($table);
        }

        return sprintf($key, $table);
    }
}
