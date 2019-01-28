<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Tests;

use Amp\Loop;
use PHPinnacle\Cassis\Context;
use PHPinnacle\Cassis\Result;
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Statement;

class SessionTest extends AsyncTest
{
    public static function setUpBeforeClass(): void
    {
        Loop::run(function () {
            $cluster = self::cluster();
            /** @var Session $session */
            $session = yield $cluster->connect();

            yield $session->query(
                "CREATE KEYSPACE IF NOT EXISTS simplex
                 WITH replication = {'class': 'SimpleStrategy', 'replication_factor': 1 };"
            );

            yield $cluster->disconnect();
        });
    }

    public function testChangeKeyspace()
    {
        $cluster = self::cluster();
        /** @var Session $session */
        $session = yield $cluster->connect();

        yield $session->query(
            "CREATE KEYSPACE IF NOT EXISTS simplex_2
             WITH replication = {'class': 'SimpleStrategy', 'replication_factor': 1 };"
        );

        self::assertSame('simplex_2', yield $session->keyspace('simplex_2'));

        yield $session->query("DROP KEYSPACE simplex_2;");

        yield $cluster->disconnect();
    }

    public function testSimpleQueries()
    {
        $cluster = self::cluster();
        /** @var Session $session */
        $session = yield $cluster->connect('simplex');

        yield $session->query("CREATE TABLE simple (id int PRIMARY KEY, enabled boolean)");

        $result1 = yield $session->query("INSERT INTO simple (id, enabled) VALUES (1, True)");
        $result2 = yield $session->query("INSERT INTO simple (id, enabled) VALUES (?, ?)", [2, false]);
        $result3 = yield $session->query("INSERT INTO simple (id, enabled) VALUES (:id, :enabled)", [
            'id' => 3,
            'enabled' => true,
        ]);

        self::assertNull($result1);
        self::assertNull($result2);
        self::assertNull($result3);

        $rows = yield $session->query("SELECT * FROM simple");

        self::assertCount(3, $rows);

        self::assertEquals(1, $rows[0]['id']);
        self::assertEquals(true, $rows[0]['enabled']);
        self::assertEquals(2, $rows[1]['id']);
        self::assertEquals(false, $rows[1]['enabled']);
        self::assertEquals(3, $rows[2]['id']);
        self::assertEquals(true, $rows[2]['enabled']);

        yield $cluster->disconnect();
    }

    public function testPaging()
    {
        $cluster = self::cluster();
        /** @var Session $session */
        $session = yield $cluster->connect('simplex');

        yield $session->query(
            "CREATE TABLE paging (
                id int,
                ordering int,
                enabled boolean,
                PRIMARY KEY ((id), ordering)
            ) WITH CLUSTERING ORDER BY (ordering ASC)"
        );

        for ($i = 1; $i <= 5; $i++) {
            yield $session->query("INSERT INTO paging (id, ordering, enabled) VALUES (1, {$i}, True)");
        }

        $context = new Context;
        $context->limit(3);

        /** @var Result\Rows $rows1 */
        $rows1 = yield $session->query("SELECT * FROM paging", [], $context);

        self::assertCount(3, $rows1);
        self::assertEquals(1, $rows1[0]['ordering']);
        self::assertEquals(2, $rows1[1]['ordering']);
        self::assertEquals(3, $rows1[2]['ordering']);

        $context->offset($rows1->cursor());

        /** @var Result\Rows $rows2 */
        $rows2 = yield $session->query("SELECT * FROM paging", [], $context);

        self::assertCount(2, $rows2);
        self::assertEquals(4, $rows2[0]['ordering']);
        self::assertEquals(5, $rows2[1]['ordering']);
        self::assertNull($rows2->cursor());

        yield $cluster->disconnect();
    }

    public function testPreparedQueries()
    {
        $cluster = self::cluster();
        /** @var Session $session */
        $session = yield $cluster->connect('simplex');

        yield $session->query("CREATE TABLE prepared (id int PRIMARY KEY, enabled boolean)");

        /** @var Result\Prepared $prepared */
        $prepared = yield $session->prepare("INSERT INTO prepared (id, enabled) VALUES (?, ?)");

        $result1 = yield $session->execute(new Statement\Prepared($prepared->id(), [1, true]));
        $result2 = yield $session->execute(new Statement\Prepared($prepared->id(), [2, false]));

        self::assertNull($result1);
        self::assertNull($result2);

        $rows = yield $session->query("SELECT * FROM prepared");

        self::assertCount(2, $rows);

        self::assertEquals(1, $rows[0]['id']);
        self::assertEquals(true, $rows[0]['enabled']);
        self::assertEquals(2, $rows[1]['id']);
        self::assertEquals(false, $rows[1]['enabled']);

        yield $cluster->disconnect();
    }
//
//    public function testBatchQueries()
//    {
//        self::loop(function () {
//            $cluster = self::cluster();
//            /** @var Session $session */
//            $session = yield $cluster->connect('simplex');
//
//            yield $session->query("CREATE TABLE batched (id int PRIMARY KEY, enabled boolean)");
//
//            $statement = Statement\Batch::logged(
//                new Statement\Simple('INSERT INTO batched (id, enabled) VALUES (1, True)'),
//                new Statement\Simple('INSERT INTO batched (id, enabled) VALUES (2, False)')
//            );
//
//            $result = yield $session->execute($statement);
//
//            self::assertNull($result);
//
//            $rows = yield $session->query("SELECT * FROM batched");
//
//            self::assertCount(2, $rows);
//
//            self::assertEquals(1, $rows[0]['id']);
//            self::assertEquals(true, $rows[0]['enabled']);
//            self::assertEquals(2, $rows[1]['id']);
//            self::assertEquals(false, $rows[1]['enabled']);
//
//            yield $cluster->disconnect();
//        });
//    }
//
//    public function testRegisterEvent()
//    {
//        self::loop(function () {
//            $cluster = self::cluster();
//            /** @var Session $session */
//            $session = yield $cluster->connect('simplex');
//
//            yield $session->query(
//                "CREATE TABLE simple (id int PRIMARY KEY, enabled boolean);"
//            );
//
//            yield $session->register(Event::SCHEMA_CHANGE, function (Event $event) use ($cluster) {
//                var_dump($event);
//            });
//
//            yield $session->query('ALTER TABLE simple ADD name text;');
//
//            yield $cluster->disconnect();
//        });
//    }

    public static function tearDownAfterClass(): void
    {
        Loop::run(function () {
            $cluster = self::cluster();
            /** @var Session $session */
            $session = yield $cluster->connect();

            yield $session->query("DROP KEYSPACE IF EXISTS simplex;");

            yield $cluster->disconnect();
        });
    }
}
