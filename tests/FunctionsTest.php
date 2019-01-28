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

class FunctionsTest extends CassisTest
{
    /**
     * @dataProvider assocProvider
     *
     * @param array $data
     */
    public function testIsAssoc(array $data)
    {
        self::assertTrue(\is_assoc($data));
    }

    public function testBigintFromNotNumeric()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value "qwe" not numeric');

        bigint_init("qwe");
    }

    public function assocProvider(): array
    {
        return [
            [['a' => 1, 'b' => 0]],
            [[1 => 1, 2 => 0]],
            [[1 => 1, 'a' => 0]],
            [[1 => 1, 0]],
        ];
    }
}
