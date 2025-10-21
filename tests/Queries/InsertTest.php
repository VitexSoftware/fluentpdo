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
 * Class InsertTest.
 *
 * @covers \Envms\FluentPDO\Queries\Insert
 */
class InsertTest extends TestCase
{
    protected Envms\FluentPDO\Query $fluent;

    protected function setUp(): void
    {
        global $pdo;

        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);

        $this->fluent = new Query($pdo);
    }

    public function testInsertStatement(): void
    {
        $query = $this->fluent->insertInto('article', [
            'user_id' => 1,
            'title' => 'new title',
            'content' => 'new content',
        ]);

        self::assertEquals('INSERT INTO article (user_id, title, content) VALUES (?, ?, ?)', $query->getQuery(false));
        self::assertEquals(['0' => '1', '1' => 'new title', '2' => 'new content'], $query->getParameters());
    }

    public function testInsertUpdate(): void
    {
        $query = $this->fluent->insertInto('article', ['id' => 1])
            ->onDuplicateKeyUpdate([
                'published_at' => '2011-12-10 12:10:00',
                'title' => 'article 1b',
                'content' => new Envms\FluentPDO\Literal('abs(-1)'), // let's update with a literal and a parameter value
            ]);

        $q = $this->fluent->from('article', 1);

        $query2 = $this->fluent->insertInto('article', ['id' => 1])
            ->onDuplicateKeyUpdate([
                'published_at' => '2011-12-10 12:10:00',
                'title' => 'article 1',
                'content' => 'content 1',
            ]);

        $q2 = $this->fluent->from('article', 1);

        self::assertEquals('INSERT INTO article (id) VALUES (?) ON DUPLICATE KEY UPDATE published_at = ?, title = ?, content = abs(-1)', $query->getQuery(false));
        self::assertEquals([0 => '1', 1 => '2011-12-10 12:10:00', 2 => 'article 1b'], $query->getParameters());
        self::assertEquals('last_inserted_id = 1', 'last_inserted_id = '.$query->execute());
        self::assertEquals(
            ['id' => '1', 'user_id' => '1', 'published_at' => '2011-12-10 12:10:00', 'title' => 'article 1b', 'content' => '1'],
            $q->fetch(),
        );
        self::assertEquals('last_inserted_id = 1', 'last_inserted_id = '.$query2->execute());
        self::assertEquals(
            ['id' => '1', 'user_id' => '1', 'published_at' => '2011-12-10 12:10:00', 'title' => 'article 1', 'content' => 'content 1'],
            $q2->fetch(),
        );
    }

    public function testInsertWithLiteral(): void
    {
        $query = $this->fluent->insertInto(
            'article',
            [
                'user_id' => 1,
                'updated_at' => new Envms\FluentPDO\Literal('NOW()'),
                'title' => 'new title',
                'content' => 'new content',
            ],
        );

        self::assertEquals('INSERT INTO article (user_id, updated_at, title, content) VALUES (?, NOW(), ?, ?)', $query->getQuery(false));
        self::assertEquals(['0' => '1', '1' => 'new title', '2' => 'new content'], $query->getParameters());
    }

    public function testInsertIgnore(): void
    {
        $query = $this->fluent->insertInto(
            'article',
            [
                'user_id' => 1,
                'title' => 'new title',
                'content' => 'new content',
            ],
        )->ignore();

        self::assertEquals('INSERT IGNORE INTO article (user_id, title, content) VALUES (?, ?, ?)', $query->getQuery(false));
        self::assertEquals(['0' => '1', '1' => 'new title', '2' => 'new content'], $query->getParameters());
    }
}
