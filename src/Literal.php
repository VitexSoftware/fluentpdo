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
 * SQL literal value.
 */
class Literal
{
    protected string $value = '';

    /**
     * Create literal value.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Get literal value.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
