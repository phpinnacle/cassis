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
use PHPinnacle\Cassis\Value\Date;
use PHPinnacle\Cassis\Type\Base;

class DateTest extends CassisTest
{
    public function testConstruct()
    {
        $datetime = new \DateTime("1970-01-01T00:00:00+0000");

        $date1 = new Date(0);
        $date2 = Date::fromSeconds(2 ** 31);

        self::assertEquals(0, $date1->value());
        self::assertEquals(0, $date2->value());
        self::assertEquals($datetime, $date1->toDateTime());
        self::assertEquals($datetime, $date2->toDateTime());
    }

    public function testFromDateTime()
    {
        $datetime = new \DateTime("1970-01-01T00:00:00+0000");
        $date = Date::fromDateTime($datetime);
        self::assertEquals(0, $date->value());
        self::assertEquals($datetime, $date->toDateTime());

        $datetime = new \DateTime("1970-01-02T00:00:00+0000");
        $date = Date::fromDateTime($datetime);
        self::assertEquals(1, $date->value());
        self::assertEquals($datetime, $date->toDateTime());

        $datetime = new \DateTime("1969-12-31T00:00:00+0000");
        $date = Date::fromDateTime($datetime);
        self::assertEquals(-1, $date->value());
        self::assertEquals($datetime, $date->toDateTime());
    }

    public function testWrite()
    {
        $buffer = new Buffer;

        $datetime = new \DateTime("1970-01-02T00:00:00+0000");
        $value    = Date::fromDateTime($datetime);

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::DATE))->read($buffer));
        self::assertEquals(0, $buffer->size());
    }
}
