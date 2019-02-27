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

use PHPinnacle\Cassis\Streams;

class StreamsTest extends CassisTest
{
    public function setUp(): void
    {
        $prop = new \ReflectionProperty(Streams::class, 'instance');
        $prop->setAccessible(true);
        $prop->setValue(Streams::class, null);
        $prop->setAccessible(false);
    }

    public function testCreate()
    {
        $streams1 = Streams::instance();
        $streams2 = Streams::instance();

        self::assertSame($streams1, $streams2);
    }

    public function testReserveRelease()
    {
        $streams = Streams::instance();

        self::assertSame(1, $streams->reserve());
        self::assertSame(2, $streams->reserve());
        self::assertSame(3, $streams->reserve());

        $streams->release(3);
        $streams->release(2);
        $streams->release(1);

        self::assertSame(1, $streams->reserve());
        self::assertSame(2, $streams->reserve());
        self::assertSame(3, $streams->reserve());
        self::assertSame(4, $streams->reserve());
    }
}
