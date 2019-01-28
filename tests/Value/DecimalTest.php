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
use PHPinnacle\Cassis\Value\Decimal;

class DecimalTest extends CassisTest
{
    /**
     * @dataProvider validStrings
     *
     * @param $input
     * @param $value
     * @param $scale
     * @param $string
     */
    public function testFromString($input, $value, $scale, $string)
    {
        $number = Decimal::fromString($input, $scale);

        self::assertEquals($value, $number->value());
        self::assertEquals($scale, $number->scale());
        self::assertEquals($string, (string) $number);
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = Decimal::fromString("123123", 2);

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::DECIMAL))->read($buffer));
    }

    /**
     * @return array
     */
    public function validStrings(): array
    {
        return [
            ["123", "123", 0, "123"],
            ["0123", "83", 0, "83"],
            ["0x123", "291", 0, "291"],
            ["0b1010101", "85", 0, "85"],
            ["-123", "-123", 0, "-123"],
            ["-0123", "-83", 0, "-83"],
            ["-0x123", "-291", 0, "-291"],
            ["-0b1010101", "-85", 0, "-85"],
            ["1313123123234234234234234234123", "1313123123234234234234234234123", 21, "1313123123.234234234234234234123"],
            ["1231", "1231", 1, "123.1"],
            ["5555", "5555", 2, "55.55"],
            ["-123123", "-123123", 3, "-123.123"],
            ["5", "5", 1, "0.5"],
            ["95", "95", 1, "9.5"],
            ["-95", "-95", 1, "-9.5"],
            ["1", "1", 5, "0.00001"],
            ["-1", "-1", 5, "-0.00001"],
            ["1", "1", 8, "0.00000001"],
            ["-1", "-1", 8, "-0.00000001"],
            ["95", "95", 9, "0.000000095"],
            ["-95", "-95", 9, "-0.000000095"],
            ["15", "15", 9, "0.000000015"],
            ["-15", "-15", 9, "-0.000000015"],
        ];
    }
}
