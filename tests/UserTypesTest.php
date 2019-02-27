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
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Value;

class UserTypesTest extends AsyncTest
{
    public static function setUpBeforeClass(): void
    {
        Loop::run(function () {
            $cluster = self::cluster();
            /** @var Session $session */
            $session = yield $cluster->connect();

            $cql = "
                CREATE KEYSPACE simplex WITH replication = {'class': 'SimpleStrategy', 'replication_factor': 1 };
                USE simplex;
                CREATE TYPE IF NOT EXISTS user (
                    id int,
                    name text
                );
                CREATE TYPE IF NOT EXISTS comment (
                    text text,
                    author frozen<user>
                );
                CREATE TABLE comments (
                    id int PRIMARY KEY,
                    comment comment
                )";

            foreach (\explode(';', $cql) as $query) {
                yield $session->query(\trim($query));
            }

            $session->close();
        });
    }
    
    public function testDataTypes()
    {
        $cluster = self::cluster();

        /** @var Session $session */
        $session = yield $cluster->connect("simplex");

        $user = new Value\UserDefined([
            'id'   => 1,
            'name' => 'John Doe',
        ]);

        $comment = new Value\UserDefined([
            'text'   => 'Cassandra is cool!',
            'author' => $user,
        ]);

        $arguments = [
            'id'      => 1,
            'comment' => $comment,
        ];

        $fields = \implode(',', \array_keys($arguments));
        $values = \implode(',', \array_fill(0, \count($arguments), '?'));
        $result = yield $session->query("INSERT INTO comments ($fields) VALUES ($values)", $arguments);

        self::assertNull($result);

        $rows   = yield $session->query("SELECT * FROM comments");
        $result = $rows[0];

        self::assertIsArray($result);
        self::assertArrayHasKey('comment', $result);
        self::assertEquals($comment, $result['comment']);

        $session->close();
    }

    public static function tearDownAfterClass(): void
    {
        Loop::run(function () {
            $cluster = self::cluster();
            /** @var Session $session */
            $session = yield $cluster->connect();

            yield $session->query("DROP KEYSPACE simplex;");

            $session->close();
        });
    }
}
