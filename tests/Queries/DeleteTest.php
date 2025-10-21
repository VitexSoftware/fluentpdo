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

require __DIR__.'/../_resources/init.php';

use Envms\FluentPDO\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class DeleteTest.
 *
 * @covers \Envms\FluentPDO\Queries\Delete
 */
class DeleteTest extends TestCase
{
    protected Query $fluent;

    protected function setUp(): void
    {
        global $pdo;

        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);

        $this->fluent = new Query($pdo);
    }

    public function testDelete(): void
    {
        $query = $this->fluent->deleteFrom('user')
            ->where('id', 1);

        self::assertEquals('DELETE FROM user WHERE id = ?', $query->getQuery(false));
        self::assertEquals(['0' => '1'], $query->getParameters());
    }

    public function testDeleteIgnore(): void
    {
        $query = $this->fluent->deleteFrom('user')
            ->ignore()
            ->where('id', 1);

        self::assertEquals('DELETE IGNORE FROM user WHERE id = ?', $query->getQuery(false));
        self::assertEquals(['0' => '1'], $query->getParameters());
    }

    public function testDeleteOrderLimit(): void
    {
        $query = $this->fluent->deleteFrom('user')
            ->where('id', 2)
            ->orderBy('name')
            ->limit(1);

        self::assertEquals('DELETE FROM user WHERE id = ? ORDER BY name LIMIT 1', $query->getQuery(false));
        self::assertEquals(['0' => '2'], $query->getParameters());
    }

    public function testDeleteExpanded(): void
    {
        $query = $this->fluent->delete('t1, t2')
            ->from('t1')
            ->innerJoin('t2 ON t1.id = t2.id')
            ->innerJoin('t3 ON t2.id = t3.id')
            ->where('t1.id', 1);

        self::assertEquals(
            'DELETE t1, t2 FROM t1 INNER JOIN t2 ON t1.id = t2.id  INNER JOIN t3 ON t2.id = t3.id WHERE t1.id = ?',
            $query->getQuery(false),
        );
        self::assertEquals(['0' => '1'], $query->getParameters());
    }

    public function testDeleteShortcut(): void
    {
        $query = $this->fluent->deleteFrom('user', 1);

        self::assertEquals('DELETE FROM user WHERE id = ?', $query->getQuery(false));
        self::assertEquals(['0' => '1'], $query->getParameters());
    }

    public function testAddFromAfterDelete(): void
    {
        $query = $this->fluent->delete('user', 1)->from('user');

        self::assertEquals('DELETE user FROM user WHERE id = ?', $query->getQuery(false));
        self::assertEquals(['0' => '1'], $query->getParameters());
    }
}
