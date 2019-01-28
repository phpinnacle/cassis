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
use PHPinnacle\Cassis\Value\Collection;

class CollectionsTest extends AsyncTest
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
                CREATE TABLE collections (
                    id int PRIMARY KEY,
                    list_value list<text>,
                    set_value set<text>,
                    map_value map<text,int>
                )";

            foreach (\explode(';', $cql) as $query) {
                yield $session->query(\trim($query));
            }

            yield $cluster->disconnect();
        });
    }
    
    public function testDataTypes()
    {
        $cluster = self::cluster();

        /** @var Session $session */
        $session = yield $cluster->connect("simplex");

        $list = Collection::list(['a','b','c']);
        $set  = Collection::set(['a','b','c','c']);
        $map  = Collection::assoc(['a' => 1,'b' => 2,'c' => 3]);

        $arguments = [
            'id' => 1,
            'list_value' => $list,
            'set_value'  => $set,
            'map_value'  => $map,
        ];

        $fields = \implode(',', \array_keys($arguments));
        $values = \implode(',', \array_fill(0, \count($arguments), '?'));
        $result = yield $session->query("INSERT INTO collections ($fields) VALUES ($values)", $arguments);

        self::assertNull($result);

        $rows   = yield $session->query("SELECT * FROM collections");
        $result = $rows[0];

        self::assertEquals($list->values(), $result['list_value']->values());
        self::assertEquals($list->keys(), $result['list_value']->keys());
        self::assertEquals($set->values(), $result['set_value']->values());
        self::assertEquals($set->keys(), $result['set_value']->keys());
        self::assertEquals($map->values(), $result['map_value']->values());
        self::assertEquals($map->keys(), $result['map_value']->keys());

        yield $cluster->disconnect();
    }

    public static function tearDownAfterClass(): void
    {
        Loop::run(function () {
            $cluster = self::cluster();
            /** @var Session $session */
            $session = yield $cluster->connect();

            yield $session->query("DROP KEYSPACE simplex;");

            yield $cluster->disconnect();
        });
    }
}
