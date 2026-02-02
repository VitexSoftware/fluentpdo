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
 * SQL literal value class.
 *
 * Represents a literal SQL value that should not be escaped or quoted.
 * Used for SQL functions, keywords, and raw SQL expressions.
 *
 * @author Chris Bornhoft, start@env.ms
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class Literal implements \Stringable
{
    protected string $value = '';

    /**
     * Create literal value.
     *
     * @param string $value The literal SQL value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get literal value as string.
     *
     * @return string The literal SQL value
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
