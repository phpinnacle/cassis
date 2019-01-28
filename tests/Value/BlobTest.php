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
use PHPinnacle\Cassis\Value\Blob;

class BlobTest extends CassisTest
{
    public function testHexEncodesString()
    {
        $blob = Blob::fromString("Hi");

        $this->assertEquals("Hi", $blob->__toString());
        $this->assertEquals([72, 105], $blob->values());
        $this->assertEquals("0x4869", $blob->bytes());
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = Blob::fromString("Hi");

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::BLOB))->read($buffer));
    }
}
