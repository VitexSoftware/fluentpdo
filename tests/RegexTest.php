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

use Envms\FluentPDO\Regex;
use PHPUnit\Framework\TestCase;

/**
 * Class StructureTest.
 *
 * @covers \Envms\FluentPDO\Structure
 */
class RegexTest extends TestCase
{
    protected Regex $regex;

    protected function setUp(): void
    {
        $this->regex = new Regex();
    }

    public function testCamelCasedSpaced(): void
    {
        $name = $this->regex->camelCaseSpaced('magicCallMethod');

        self::assertEquals('magic Call Method', $name);
    }

    public function testSplitClauses(): void
    {
        $query = $this->regex->splitClauses('SELECT * FROM user WHERE id = 1 OR id = 2 ORDER BY id ASC');

        self::assertEquals("SELECT * \nFROM user \nWHERE id = 1 OR id = 2 \nORDER BY id ASC", $query);
    }

    public function testSplitSubClauses(): void
    {
        $query = $this->regex->splitSubClauses('SELECT * FROM user LEFT JOIN article WHERE 1 OR 2');

        self::assertEquals("SELECT * FROM user \n    LEFT JOIN article WHERE 1 \n    OR 2", $query);
    }

    public function testRemoveLineEndWhitespace(): void
    {
        $query = $this->regex->removeLineEndWhitespace("SELECT *   \n FROM user \n");

        self::assertEquals("SELECT *\n FROM user\n", $query);
    }

    public function testRemoveAdditionalJoins(): void
    {
        $join = $this->regex->removeAdditionalJoins('user.article:id');

        self::assertEquals('article.id', $join);
    }

    public function testSqlParameter(): void
    {
        $isParam = $this->regex->sqlParameter('id = :id');
        self::assertEquals(1, $isParam);

        $isParam = $this->regex->sqlParameter('name = ?');
        self::assertEquals(1, $isParam);

        $isParam = $this->regex->sqlParameter('count IN (22, 77)');
        self::assertEquals(0, $isParam);
    }

    public function testTableAlias(): void
    {
        $isAlias = $this->regex->tableAlias('user AS u');
        self::assertEquals(1, $isAlias);

        $isAlias = $this->regex->tableAlias('user.*');
        self::assertEquals(1, $isAlias);

        $isAlias = $this->regex->tableAlias('   ');
        self::assertEquals(0, $isAlias);

        $isAlias = $this->regex->tableAlias('0.00 AS ཎ');
        self::assertEquals(1, $isAlias);
    }

    public function testTableJoin(): void
    {
        $join = $this->regex->tableJoin('user');
        self::assertEquals(1, $join);

        $join = $this->regex->tableJoin('`user`.');
        self::assertEquals(1, $join);

        $join = $this->regex->tableJoin("'''");
        self::assertEquals(0, $join);

        $join = $this->regex->tableJoin('ឃឡឱ.');
        self::assertEquals(1, $join);
    }

    public function testTableJoinFull(): void
    {
        $join = $this->regex->tableJoinFull('user.');
        self::assertEquals(1, $join);

        $join = $this->regex->tableJoinFull('`user`.`column`');
        self::assertEquals(1, $join);

        $join = $this->regex->tableJoinFull('user .column');
        self::assertEquals(0, $join);

        $join = $this->regex->tableJoinFull('ㇽㇺㇴ.ㇱ');
        self::assertEquals(1, $join);
    }
}
