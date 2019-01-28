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
use PHPinnacle\Cassis\Value\Inet;

class InetTest extends CassisTest
{
    public function testFromToString()
    {
        $value = Inet::fromString('192.168.1.1');

        self::assertEquals('192.168.1.1', (string) $value);
    }

    public function testInvalidString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ip address: "192.168.1".');

        Inet::fromString('192.168.1');
    }

    public function testFromBytes()
    {
        $bytes = \inet_pton('192.168.1.1');
        $value = Inet::fromBytes($bytes);

        self::assertEquals('192.168.1.1', (string) $value);
    }

    public function testFromInvalidBytes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cant read ip address from bytes string.');

        Inet::fromBytes('wrong');
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = Inet::fromString('192.168.1.1');

        $buffer->appendValue($value);

        self::assertEquals($value, (new Base(Base::INET))->read($buffer));
    }
}
