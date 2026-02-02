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

require __DIR__.'/_resources/init.php';

use Envms\FluentPDO\Structure;
use PHPUnit\Framework\TestCase;

/**
 * Class StructureTest.
 *
 * @covers \Envms\FluentPDO\Structure
 */
class StructureTest extends TestCase
{
    public function testBasicKey(): void
    {
        $structure = new Structure();

        self::assertEquals('id', $structure->getPrimaryKey('user'));
        self::assertEquals('user_id', $structure->getForeignKey('user'));
    }

    public function testCustomKey(): void
    {
        $structure = new Structure('whatAnId', '%s_\xid');

        self::assertEquals('whatAnId', $structure->getPrimaryKey('user'));
        self::assertEquals('user_\xid', $structure->getForeignKey('user'));
    }

    public function testMethodKey(): void
    {
        $structure = new Structure('id', ['StructureTest', 'suffix']);

        self::assertEquals('id', $structure->getPrimaryKey('user'));
        self::assertEquals('user_id', $structure->getForeignKey('user'));
    }

    /**
     * @param mixed $table
     *
     * @return string
     */
    public static function suffix($table)
    {
        return $table.'_id';
    }
}
