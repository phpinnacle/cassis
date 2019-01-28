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

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Context;
use PHPinnacle\Cassis\Type;

class ContextTest extends CassisTest
{
    public function testWriteNoParameters()
    {
        $context = new Context;
        $buffer  = new Buffer;
    
        $context->writeParameters($buffer);

        self::assertEquals(Context::CONSISTENCY_ALL, $context->consistency());
        self::assertEquals(Context::CONSISTENCY_ALL, $buffer->consumeShort());
        self::assertEquals(0, $buffer->consumeByte());
    }

    public function testConsistency()
    {
        $context = new Context;
    
        $context->consistencyAny();
        self::assertEquals(Context::CONSISTENCY_ANY, $context->consistency());

        $context->consistencyOne();
        self::assertEquals(Context::CONSISTENCY_ONE, $context->consistency());

        $context->consistencyTwo();
        self::assertEquals(Context::CONSISTENCY_TWO, $context->consistency());
    
        $context->consistencyThree();
        self::assertEquals(Context::CONSISTENCY_THREE, $context->consistency());
    
        $context->consistencyQuorum();
        self::assertEquals(Context::CONSISTENCY_QUORUM, $context->consistency());
    
        $context->consistencyAll();
        self::assertEquals(Context::CONSISTENCY_ALL, $context->consistency());
    
        $context->consistencyEachQuorum();
        self::assertEquals(Context::CONSISTENCY_EACH_QUORUM, $context->consistency());
    
        $context->consistencyLocalQuorum();
        self::assertEquals(Context::CONSISTENCY_LOCAL_QUORUM, $context->consistency());
    
        $context->consistencyLocalOne();
        self::assertEquals(Context::CONSISTENCY_LOCAL_ONE, $context->consistency());
    }

    public function testWriteParameters()
    {
        $context = new Context;
        $buffer  = new Buffer;
        
        $context
            ->consistencyOne()
            ->serialConsistency(Context::CONSISTENCY_LOCAL_SERIAL)
            ->defaultTimestamp($time = time())
            ->limit(1000)
            ->offset('state')
            ->skipMetadata()
            ->arguments([
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ])
        ;
        
        $context->writeParameters($buffer);

        self::assertEquals(Context::CONSISTENCY_ONE, $buffer->consumeShort());
        
        // Flags
        self::assertEquals(127, $buffer->consumeByte());

        // Values
        self::assertEquals(3, $buffer->consumeShort());
        self::assertEquals('a', $buffer->consumeString());
        self::assertEquals(1, (new Type\Base(Type::INT))->read($buffer));
        self::assertEquals('b', $buffer->consumeString());
        self::assertEquals(2, (new Type\Base(Type::INT))->read($buffer));
        self::assertEquals('c', $buffer->consumeString());
        self::assertEquals(3, (new Type\Base(Type::INT))->read($buffer));

        // Paging
        self::assertEquals(1000, $buffer->consumeInt());
        self::assertEquals('state', $buffer->consumeLongString());
        
        // Serial consistency
        self::assertEquals(Context::CONSISTENCY_LOCAL_SERIAL, $buffer->consumeShort());

        // Default timestamp
        self::assertEquals($time, $buffer->consumeLong());
    }

    public function testArgumentsList()
    {
        $context = new Context;
        $buffer  = new Buffer;

        $context->arguments([1, true, 'yes']);

        $context->writeParameters($buffer);

        self::assertEquals(Context::CONSISTENCY_ALL, $buffer->consumeShort());

        $flags = $buffer->consumeByte();

        self::assertEquals(1, $flags);
        self::assertEquals(1, $flags & Context::FLAG_VALUES);
        self::assertEquals(0, $flags & Context::FLAG_NAMES_FOR_VALUES);

        // Values
        self::assertEquals(3, $buffer->consumeShort());
        self::assertEquals(1, (new Type\Base(Type::INT))->read($buffer));
        self::assertEquals(true, (new Type\Base(Type::BOOLEAN))->read($buffer));
        self::assertEquals('yes', (new Type\Base(Type::TEXT))->read($buffer));
    }

    public function testEmptyArguments()
    {
        $context = new Context;
        $buffer  = new Buffer;

        $context->arguments([]);

        $context->writeParameters($buffer);

        self::assertEquals(Context::CONSISTENCY_ALL, $buffer->consumeShort());

        $flags = $buffer->consumeByte();

        self::assertEquals(0, $flags);
        self::assertEquals(0, $flags & Context::FLAG_VALUES);
        self::assertEquals(0, $flags & Context::FLAG_NAMES_FOR_VALUES);
    }
}
