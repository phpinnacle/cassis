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
use PHPinnacle\Cassis\Value\Double;

class DoubleTest extends CassisTest
{
    /**
     * @dataProvider validStrings
     *
     * @param $input
     * @param $value
     */
    public function testConstruct($input, $value)
    {
        $number1 = Double::fromString($input);
        $number2 = Double::fromFloat($value);

        self::assertEquals($value, $number1->value());
        self::assertEquals($input, (string) $number1);
        self::assertEquals($value, $number2->value());
        self::assertEquals($input, (string) $number2);
    }

    public function testThrowsWhenCreatingNotAnInteger()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value "qwe" not numeric');

        Double::fromString("qwe");
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = Double::fromString("13.45");

        $buffer->appendValue($value);

        self::assertEquals(13.45, (new Base(Base::DOUBLE))->read($buffer));
    }

    /**
     * @return array
     */
    public function validStrings(): array
    {
        return [
            ["13.45", 13.45],
            ["3.14", 3.14],
        ];
    }
}
