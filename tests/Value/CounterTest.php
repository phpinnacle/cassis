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
use PHPinnacle\Cassis\Value\Counter;

class CounterTest extends CassisTest
{
    public function testCreate()
    {
        $counter = new Counter(100);

        self::assertEquals(100, $counter->value());
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = new Counter(100);

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::COUNTER))->read($buffer));
    }
}
