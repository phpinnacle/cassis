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
use PHPinnacle\Cassis\Cluster;
use PHPinnacle\Cassis\Exception\ClientException;
use PHPinnacle\Cassis\Exception\ServerException;
use PHPinnacle\Cassis\Session;

class ClusterTest extends AsyncTest
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

    public function testBuild()
    {
        $cluster = Cluster::build('tcp://localhost:9042');

        self::assertInstanceOf(Cluster::class, $cluster);
    }

    public function testOptions()
    {
        $cluster = self::cluster();
        $promise = $cluster->options();

        self::assertPromise($promise);

        $options = yield $promise;

        self::assertIsArray($options);

        foreach ($options as $option => $values) {
            self::assertIsArray($values);
        }
    }

    public function testConnect()
    {
        $cluster = self::cluster();
        $promise = $cluster->connect();

        self::assertPromise($promise);

        /** @var Session $session */
        $session = yield $promise;

        self::assertInstanceOf(Session::class, $session);

        $session->close();
    }

    public function testConnectWithKeyspace()
    {
        $cluster = self::cluster();
        /** @var Session $session */
        $session = yield $cluster->connect('simplex');

        $ref = new \ReflectionProperty(Session::class, 'keyspace');
        $ref->setAccessible(true);

        self::assertSame('simplex', $ref->getValue($session));

        $session->close();
    }

    public function testConnectWithUnknownKeyspace()
    {
        $this->expectException(ServerException::class);

        $cluster = self::cluster();

        yield $cluster->connect('unknown');
    }

    public function testConnectFailure()
    {
        $this->expectException(ClientException::class);

        $cluster = Cluster::build('tcp://localhost:19042');

        yield $cluster->connect();
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
