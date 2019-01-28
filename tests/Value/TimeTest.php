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
use PHPinnacle\Cassis\Value\Time;

class TimeTest extends CassisTest
{
    public function testConstruct()
    {
        $time = Time::fromNanoSeconds(0);
        self::assertEquals(0, $time->value());

        $time = Time::fromNanoSeconds(42);
        self::assertEquals(42, $time->value());

        $time = Time::fromNanoSeconds(86399999999999);
        self::assertEquals(86399999999999, $time->value());
    }

    public function testConstructNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Time must be nanoseconds since midnight, -1 given');

        Time::fromNanoSeconds(-1);
    }

    public function testConstructTooBig()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Time must be nanoseconds since midnight, 86400000000000 given');

        Time::fromNanoSeconds(86400000000000);
    }

    public function testFromDateTime()
    {
        $datetime = new \DateTime("1970-01-01T00:00:00+0000");
        $time = Time::fromDateTime($datetime);
        self::assertEquals(0, $time->value());

        $datetime = new \DateTime("1970-01-01T00:00:01+0000");
        $time = Time::fromDateTime($datetime);
        self::assertEquals(1000000000, $time->value());

        $datetime = new \DateTime("1970-01-01T23:59:59+0000");
        $time = Time::fromDateTime($datetime);
        self::assertEquals(86399000000000, $time->value());
    }

    public function testFromToInterval()
    {
        $datetime1 = new \DateTime("1970-01-01T00:00:00+0000");
        $datetime2 = new \DateTime("1970-01-01T00:00:02+0000");
        $interval  = $datetime2->diff($datetime1);

        $time = Time::fromInterval($datetime2->diff($datetime1));

        self::assertEquals(2000000000, $time->value());
        self::assertEquals($interval, $time->toDateInterval());
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = Time::fromNanoSeconds(42);

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::TIME))->read($buffer));
    }
}
