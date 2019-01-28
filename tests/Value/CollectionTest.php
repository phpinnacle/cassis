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
use PHPinnacle\Cassis\Type;
use PHPinnacle\Cassis\Type\Base;
use PHPinnacle\Cassis\Value\Collection;

class CollectionTest extends CassisTest
{
    public function testList()
    {
        $values = [1,2,3];
        $list   = Collection::list($values);

        self::assertEquals($values, $list->values());
        self::assertEquals(\array_keys($values), $list->keys());

        $values = [1,2,3,3];
        $set    = Collection::set($values);

        self::assertEquals([1,2,3], $set->values());
        self::assertEquals([0,1,2], $set->keys());

        $values = ['a' => 1, 'b' => 2];
        $set    = Collection::assoc($values);

        self::assertEquals(\array_values($values), $set->values());
        self::assertEquals(\array_keys($values), $set->keys());
    }

    public function testIterate()
    {
        $values = [1,2,3];
        $list   = Collection::list($values);

        foreach ($list as $key => $value) {
            self::assertEquals($values[$key], $value);
        }
    }

    public function testCount()
    {
        $list1 = Collection::list([1,2,3]);
        $list2 = Collection::list([]);

        self::assertCount(3, $list1);
        self::assertCount(0, $list2);
    }

    public function testExists()
    {
        $list = Collection::list([1,2,3]);

        self::assertTrue(isset($list[1]));
        self::assertFalse(isset($list[3]));
    }

    public function testGet()
    {
        $list = Collection::list([1,2,3]);

        self::assertEquals(1, $list[0]);
        self::assertEquals(2, $list[1]);
        self::assertEquals(3, $list[2]);
    }

    public function testUnset()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Collection is immutable.');

        $list = Collection::list([1,2,3]);

        unset($list[1]);
    }

    public function testSet()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Collection is immutable.');

        $list = Collection::list([1,2,3]);
        $list[1] = 5;
    }

    public function testWrite()
    {
        $buffer = new Buffer;

        $list = Collection::list([1,2,3]);
        $set  = Collection::set([1,2,3]);
        $map  = Collection::assoc(['a' => 1, 'b' => 2]);

        $buffer
            ->appendValue($list)
            ->appendValue($set)
            ->appendValue($map)
        ;

        self::assertEquals($list, (Type\Collection::list(new Base(Base::INT)))->read($buffer));
        self::assertEquals($set, (Type\Collection::set(new Base(Base::INT)))->read($buffer));
        self::assertEquals($map, (Type\Collection::map(new Base(Base::TEXT), new Base(Base::INT))->read($buffer)));
        self::assertEquals(0, $buffer->size());
    }
}
