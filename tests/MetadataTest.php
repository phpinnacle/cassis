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
use PHPinnacle\Cassis\Metadata;
use PHPinnacle\Cassis\Type;

class MetadataTest extends CassisTest
{
    public function testCreate()
    {
        $columns = [
            new Column('simplex', 'table', 'int', new Type\Base(Type::INT)),
            new Column('simplex', 'table', 'bool', new Type\Base(Type::BOOLEAN))
        ];

        $metadata = new Metadata($columns, 'cursor');

        self::assertEquals($columns, $metadata->columns());
        self::assertEquals('cursor', $metadata->cursor());
    }

    public function testCreateFromBufferWithPaging()
    {
        $buffer = new Buffer;
        $buffer
            ->appendInt(Metadata::FLAG_HAS_MORE_PAGES)
            ->appendInt(0)
            ->appendLongString('cursor')
        ;

        $metadata = Metadata::create($buffer);

        self::assertEquals([], $metadata->columns());
        self::assertEquals('cursor', $metadata->cursor());
    }

    public function testCreateFromBufferWithoutColumns()
    {
        $buffer = new Buffer;
        $buffer
            ->appendInt(Metadata::FLAG_NO_METADATA)
            ->appendInt(0)
        ;

        $metadata = Metadata::create($buffer);

        self::assertEquals([], $metadata->columns());
        self::assertEquals(null, $metadata->cursor());
    }

    public function testCreateFromBufferWithGlobalColumns()
    {
        $buffer = new Buffer;
        $buffer
            ->appendInt(Metadata::FLAG_GLOBAL_TABLES_SPEC)
            ->appendInt(2)
            ->appendString('simplex')
            ->appendString('table')
            ->appendString('int')
            ->appendShort(Type::INT)
            ->appendString('bool')
            ->appendShort(Type::BOOLEAN)
        ;

        $metadata = Metadata::create($buffer);

        self::assertEquals([
            new Column('simplex', 'table', 'int', new Type\Base(Type::INT)),
            new Column('simplex', 'table', 'bool', new Type\Base(Type::BOOLEAN))
        ], $metadata->columns());
    }

    public function testCreateFromBufferWithLocalColumns()
    {
        $buffer = new Buffer;
        $buffer
            ->appendInt(0)
            ->appendInt(2)
            ->appendString('simplex')
            ->appendString('table')
            ->appendString('int')
            ->appendShort(Type::INT)
            ->appendString('simplex')
            ->appendString('table')
            ->appendString('bool')
            ->appendShort(Type::BOOLEAN)
        ;

        $metadata = Metadata::create($buffer);

        self::assertEquals([
            new Column('simplex', 'table', 'int', new Type\Base(Type::INT)),
            new Column('simplex', 'table', 'bool', new Type\Base(Type::BOOLEAN))
        ], $metadata->columns());
    }
}
