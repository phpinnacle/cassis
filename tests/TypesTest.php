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
use PHPinnacle\Cassis\Result\Rows;
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Value\Blob;
use PHPinnacle\Cassis\Value\Date;
use PHPinnacle\Cassis\Value\Decimal;
use PHPinnacle\Cassis\Value\Double;
use PHPinnacle\Cassis\Value\Inet;
use PHPinnacle\Cassis\Value\Integer;
use PHPinnacle\Cassis\Value\Time;
use PHPinnacle\Cassis\Value\Timestamp;
use PHPinnacle\Cassis\Value\Varint;
use Ramsey\Uuid\Uuid;

class TypesTest extends AsyncTest
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
                CREATE TABLE values (
                    id int PRIMARY KEY,
                    boolean_value boolean,
                    tinyint_value tinyint,
                    smallint_value smallint,
                    int_value int,
                    bigint_value bigint,
                    varint_value varint,
                    float_value float,
                    double_value double,
                    decimal_value decimal,
                    timestamp_value timestamp,
                    date_value date,
                    time_value time,
                    uuid_value uuid,
                    timeuuid_value timeuuid,
                    inet_value inet,
                    blob_value blob
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

        $arguments = [
            'id' => 1,
            'boolean_value' => true,
            'tinyint_value' => Integer::tiny(127),
            'smallint_value' => Integer::small(512),
            'int_value' => 8193,
            'bigint_value' => Integer::big(-765438000),
            'varint_value' => Varint::fromString('67890656781923123918798273492834712837198237'),
            'float_value' => 3.14,
            'double_value' => Double::fromFloat(3.141592653589793),
            'decimal_value' => Decimal::fromString('1313123123234234234234234234123', 21),
            'timestamp_value' => Timestamp::fromMicroSeconds(1425691864001),
            'date_value' => Date::fromDateTime(new \DateTimeImmutable('2017-05-05')),
            'time_value' => Time::fromDateTime(new \DateTimeImmutable('13:29:01.050')),
            'uuid_value' => Uuid::fromString('ab3352d9-4f7f-4007-a35a-e62aa7ab0b19'),
            'timeuuid_value' => Uuid::fromString('7f0a920f-c7fd-11e4-7f7f-7f7f7f7f7f7f'),
            'inet_value' => Inet::fromString('200.199.198.197'),
            'blob_value' => Blob::fromString('0x000000'),
        ];

        $fields = \implode(',', \array_keys($arguments));
        $values = \implode(',', \array_fill(0, \count($arguments), '?'));
        $result = yield $session->query("INSERT INTO values ($fields) VALUES ($values)", $arguments);

        self::assertNull($result);

        /** @var Rows $rows */
        $rows   = yield $session->query("SELECT * FROM values");
        $result = $rows->current();

        self::assertEquals(true, $result['boolean_value']);
        self::assertEquals(127, $result['tinyint_value']);
        self::assertEquals(512, $result['smallint_value']);
        self::assertEquals(8193, $result['int_value']);
        self::assertEquals(-765438000, $result['bigint_value']);
        self::assertEquals('67890656781923123918798273492834712837198237', (string) $result['varint_value']);
        self::assertEquals(3.14, round($result['float_value'], 2));
        self::assertEquals(3.1415926535898, $result['double_value']);
        self::assertEquals('1313123123.234234234234234234123', (string) $result['decimal_value']);
        self::assertEquals(1425691864001, $result['timestamp_value']->value());
        self::assertEquals('2017-05-05', $result['date_value']->toDateTime()->format('Y-m-d'));
        self::assertEquals('13:29:01.050000', $result['time_value']->toDateTime()->format('H:i:s.u'));
        self::assertEquals('ab3352d9-4f7f-4007-a35a-e62aa7ab0b19', (string) $result['uuid_value']);
        self::assertEquals('7f0a920f-c7fd-11e4-7f7f-7f7f7f7f7f7f', (string) $result['timeuuid_value']);
        self::assertEquals('200.199.198.197', (string) $result['inet_value']);
        self::assertEquals('0x3078303030303030', $result['blob_value']->bytes());

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
