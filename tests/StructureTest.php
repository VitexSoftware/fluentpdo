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
