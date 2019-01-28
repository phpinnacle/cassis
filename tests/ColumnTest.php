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
use PHPinnacle\Cassis\Column;
use PHPinnacle\Cassis\Type;

class ColumnTest extends CassisTest
{
    public function testCreate()
    {
        $type   = new Type\Base(Type::CUSTOM);
        $column = new Column('simplex', 'table', 'name', $type);

        self::assertEquals('simplex', $column->keyspace());
        self::assertEquals('table', $column->table());
        self::assertEquals('name', $column->name());
        self::assertEquals($type, $column->type());
    }

    public function testFullFromBuffer()
    {
        $buffer = new Buffer;
        $buffer
            ->appendString('simplex')
            ->appendString('table')
            ->appendString('name')
            ->appendShort(Type::INT)
        ;

        $column = Column::full($buffer);

        self::assertEquals('simplex', $column->keyspace());
        self::assertEquals('table', $column->table());
        self::assertEquals('name', $column->name());
        self::assertEquals(new Type\Base(Type::INT), $column->type());
    }

    public function testPartialFromBuffer()
    {
        $buffer = new Buffer;
        $buffer
            ->appendString('name')
            ->appendShort(Type::INT)
        ;

        $column = Column::partial('simplex', 'table', $buffer);

        self::assertEquals('simplex', $column->keyspace());
        self::assertEquals('table', $column->table());
        self::assertEquals('name', $column->name());
        self::assertEquals(new Type\Base(Type::INT), $column->type());
    }

    public function testValue()
    {
        $buffer = new Buffer;
        $buffer
            ->appendInt(4)
            ->appendInt(123)
        ;

        $type   = new Type\Base(Type::INT);
        $column = new Column('simplex', 'table', 'name', $type);

        self::assertEquals(123, $column->value($buffer));
    }
}
