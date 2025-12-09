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

/**
 * Class Structure.
 */
class Structure
{
    private string $primaryKey;
    private string $foreignKey;

    /**
     * Structure constructor.
     */
    public function __construct(string $primaryKey = 'id', string $foreignKey = '%s_id')
    {
        if ($foreignKey === null) {
            $foreignKey = $primaryKey;
        }

        $this->primaryKey = $primaryKey;
        $this->foreignKey = $foreignKey;
    }

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
