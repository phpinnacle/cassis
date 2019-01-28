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
use PHPinnacle\Cassis\Value\Varint;

class VarintTest extends CassisTest
{
    const VALUE = '67890656781923123918798273492834712837198237';

    public function testConstruct()
    {
        $gmp   = \gmp_init(self::VALUE);
        $value = new Varint($gmp);

        self::assertEquals(self::VALUE, (string) $value);
    }

    public function testFromToString()
    {
        $value = Varint::fromString(self::VALUE);

        self::assertEquals(self::VALUE, (string) $value);
    }

    public function testFromBytes()
    {
        $bytes = \gmp_export(self::VALUE);
        $value = Varint::fromBytes($bytes);

        self::assertEquals(self::VALUE, (string) $value);
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = Varint::fromString(self::VALUE);

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::VARINT))->read($buffer));
        self::assertEquals(0, $buffer->size());
    }
}
