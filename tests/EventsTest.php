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
use PHPinnacle\Cassis\Event;
use PHPinnacle\Cassis\Events;
use PHPinnacle\Cassis\Session;

class EventsTest extends AsyncTest
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

            $session->close();
        });
    }

    public function testRegisterEvent()
    {
        $cluster = self::cluster();
        /** @var Session $session */
        $session = yield $cluster->connect('simplex');

        yield $session->query(
            'CREATE TABLE simple (id int PRIMARY KEY, enabled boolean);'
        );

        /** @var Events $events */
        $events = yield $cluster->events();

        yield $events->onSchemaChange(function (Event\SchemaChange $event) {
            self::assertEquals('UPDATED', $event->change());
            self::assertEquals('TABLE', $event->target());
            self::assertEquals('simplex', $event->keyspace());
            self::assertEquals('simple', $event->name());
        });

        yield $session->query('ALTER TABLE simple ADD name text;');

        $session->close();
    }

    public static function tearDownAfterClass(): void
    {
        Loop::run(function () {
            $cluster = self::cluster();
            /** @var Session $session */
            $session = yield $cluster->connect();

            yield $session->query("DROP KEYSPACE IF EXISTS simplex;");
            $session->close();
        });
    }
}
