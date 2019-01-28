<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Tests\Value;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Tests\CassisTest;
use PHPinnacle\Cassis\Type\Base;
use PHPinnacle\Cassis\Value\Timestamp;

class TimestampTest extends CassisTest
{
    public function testConstruct()
    {
        $time = Timestamp::fromMicroSeconds(0);
        self::assertEquals(0, $time->value());

        $time = Timestamp::fromMicroSeconds(42);
        self::assertEquals(42, $time->value());
    }

    public function testConstructNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Timestamp must be positive, -1 given');

        Timestamp::fromMicroSeconds(-1);
    }

    public function testFromToDateTime()
    {
        $datetime  = new \DateTime("1970-01-01T00:00:00+0000");
        $timestamp = Timestamp::fromDateTime($datetime);
        self::assertEquals(0, $timestamp->value());
        self::assertEquals($datetime, $timestamp->toDateTime());

        $datetime  = new \DateTime("1970-01-01T00:00:01+0000");
        $timestamp = Timestamp::fromDateTime($datetime);
        self::assertEquals(1000000, $timestamp->value());
        self::assertEquals($datetime, $timestamp->toDateTime());

        $datetime  = new \DateTime("1970-01-01T23:59:59.000003+0000");
        $timestamp = Timestamp::fromDateTime($datetime);
        self::assertEquals(86399000003, $timestamp->value());
        self::assertEquals($datetime, $timestamp->toDateTime());
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = Timestamp::fromMicroSeconds(42);

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::TIMESTAMP))->read($buffer));
    }
}
