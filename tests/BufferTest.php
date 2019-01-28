<?php
/**
 * This file is part of PHPinnacle/Ridge.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Tests;

use PHPinnacle\Cassis\Buffer;
use Ramsey\Uuid\Uuid;

class BufferTest extends CassisTest
{
    public function testAppend()
    {
        $buffer = new Buffer;

        self::assertSame($buffer, $buffer->append('abcd'));
        self::assertSame('abcd', $buffer->consume(4));
    }

    public function testSlice()
    {
        $buffer = new Buffer('abcd');
        $slice  = $buffer->slice(2);

        self::assertSame('ab', $slice->flush());
        self::assertSame('cd', $buffer->flush());
    }

    public function testDiscard()
    {
        $buffer = new Buffer('abcd');
        $buffer->discard(2);

        self::assertSame('cd', $buffer->flush());
    }

    public function testFlush()
    {
        $buffer = new Buffer('abcd');

        self::assertSame(4, $buffer->size());
        self::assertSame('abcd', $buffer->flush());
        self::assertSame(0, $buffer->size());
    }

    public function testByte()
    {
        $buffer = new Buffer;
        $buffer->appendByte(100);

        self::assertSame(100, $buffer->readByte());
        self::assertSame(100, $buffer->consumeByte());
        self::assertSame(0, $buffer->size());
    }

    public function testBytes()
    {
        $buffer = new Buffer;
        $buffer->appendByte(100);
        $buffer->appendByte(101);
        $buffer->appendByte(42);

        self::assertSame([100, 101, 42], $buffer->consumeBytes(3));
    }

    public function testBytesMap()
    {
        $buffer = new Buffer;
        $buffer->appendShort(2);
        $buffer->appendString('a');
        $buffer->appendBytes([1, 2]);
        $buffer->appendString('b');
        $buffer->appendBytes([3, 4]);

        self::assertSame([
            'a' => [1,2],
            'b' => [3,4],
        ], $buffer->consumeBytesMap());
    }

    public function testShort()
    {
        $buffer = new Buffer;
        $buffer->appendShort(100);

        self::assertSame(100, $buffer->readShort());
        self::assertSame(100, $buffer->consumeShort());
        self::assertSame(0, $buffer->size());

        $buffer->appendShort(-100);

        self::assertSame(100, $buffer->readShort());
        self::assertSame(100, $buffer->consumeShort());
        self::assertSame(0, $buffer->size());
    }

    public function testTinyInt()
    {
        $buffer = new Buffer;
        $buffer->appendTinyInt(100);

        self::assertSame(100, $buffer->readTinyInt());
        self::assertSame(100, $buffer->consumeTinyInt());
        self::assertSame(0, $buffer->size());
    }

    public function testSmallInt()
    {
        $buffer = new Buffer;
        $buffer->appendSmallInt(100);

        self::assertSame(100, $buffer->readSmallInt());
        self::assertSame(100, $buffer->consumeSmallInt());
        self::assertSame(0, $buffer->size());
    }

    public function testInt()
    {
        $buffer = new Buffer;
        $buffer->appendInt(100);

        self::assertSame(100, $buffer->readInt());
        self::assertSame(100, $buffer->consumeInt());
        self::assertSame(0, $buffer->size());
    }

    public function testUint()
    {
        $buffer = new Buffer;
        $buffer->appendUint(100);

        self::assertSame(100, $buffer->readUint());
        self::assertSame(100, $buffer->consumeUint());
        self::assertSame(0, $buffer->size());

        $buffer->appendUint(-100);

        self::assertSame(100, $buffer->readUint());
        self::assertSame(100, $buffer->consumeUint());
        self::assertSame(0, $buffer->size());
    }

    public function testLong()
    {
        $buffer = new Buffer;
        $buffer->appendLong(100);

        self::assertSame(100, $buffer->readLong());
        self::assertSame(100, $buffer->consumeLong());
        self::assertSame(0, $buffer->size());
    }

    public function testFloat()
    {
        $buffer = new Buffer;
        $buffer->appendFloat(100.01);

        self::assertEquals(100.01, round($buffer->readFloat(), 2));
        self::assertEquals(100.01, round($buffer->consumeFloat(), 2));
        self::assertEquals(0, $buffer->size());
    }

    public function testDouble()
    {
        $buffer = new Buffer;
        $buffer->appendDouble(100.01);

        self::assertEquals(100.01, $buffer->readDouble());
        self::assertEquals(100.01, $buffer->consumeDouble());
        self::assertEquals(0, $buffer->size());
    }

    public function testString()
    {
        $buffer = new Buffer;
        $buffer->appendString('abcd');

        self::assertSame(6, $buffer->size());
        self::assertSame('abcd', $buffer->readString());
        self::assertSame('abcd', $buffer->consumeString());
        self::assertSame(0, $buffer->size());
    }

    public function testLongString()
    {
        $buffer = new Buffer;
        $buffer->appendLongString('abcd');

        self::assertSame(8, $buffer->size());
        self::assertSame('abcd', $buffer->readLongString());
        self::assertSame('abcd', $buffer->consumeLongString());
        self::assertSame(0, $buffer->size());
    }

    public function testStringList()
    {
        $buffer = new Buffer;
        $buffer->appendStringList([
            'abcd',
            'fegh',
        ]);

        self::assertSame(14, $buffer->size());
        self::assertSame([
            'abcd',
            'fegh',
        ], $buffer->consumeStringList());
        self::assertSame(0, $buffer->size());
    }

    public function testStringMap()
    {
        $buffer = new Buffer;
        $buffer->appendStringMap([
            'a' => 'abcd',
            'b' => 'fegh',
        ]);

        self::assertSame(20, $buffer->size());
        self::assertSame(2, $buffer->consumeShort());
        self::assertSame('a', $buffer->consumeString());
        self::assertSame('abcd', $buffer->consumeString());
        self::assertSame('b', $buffer->consumeString());
        self::assertSame('fegh', $buffer->consumeString());
        self::assertSame(0, $buffer->size());
    }

    public function testSimpleValues()
    {
        $values = [
            null,
            true,
            false,
            0,
            10,
            0.2,
            'abcd',
            [
                'a',
                'b'
            ],
            [
                'a' => 'b',
                'c' => 'd',
            ],
            new \DateTime,
            Uuid::fromString('550e8400-e29b-41d4-a716-446655440000'),
        ];

        $buffer = new Buffer;

        foreach ($values as $value) {
            $buffer->appendValue($value);
        }

        self::assertSame(124, $buffer->size());
    }
//
//    public function testCustomValues()
//    {
//        $values = [
//        ];
//
//        $buffer = new Buffer;
//
//        foreach ($values as $value) {
//            $buffer->appendValue($value);
//        }
//    }
}
