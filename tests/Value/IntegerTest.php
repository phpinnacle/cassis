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
use PHPinnacle\Cassis\Value\Integer;

class IntegerTest extends CassisTest
{
    public function testWrite()
    {
        $buffer = new Buffer;

        $tiny  = Integer::tiny(1);
        $small = Integer::small(500);
        $int   = Integer::int(8193);
        $big   = Integer::big(765438000);

        $buffer
            ->appendValue($tiny)
            ->appendValue($small)
            ->appendValue($int)
            ->appendValue($big)
        ;

        self::assertEquals(1, (new Base(Base::TINYINT))->read($buffer));
        self::assertEquals(500, (new Base(Base::SMALLINT))->read($buffer));
        self::assertEquals(8193, (new Base(Base::INT))->read($buffer));
        self::assertEquals(765438000, (new Base(Base::BIGINT))->read($buffer));
        self::assertEquals(0, $buffer->size());
    }
}
