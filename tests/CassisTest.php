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

use Amp\Promise;
use PHPUnit\Framework\TestCase;

abstract class CassisTest extends TestCase
{
    /**
     * @param mixed $value
     *
     * @return void
     */
    public static function assertPromise($value): void
    {
        self::assertInstanceOf(Promise::class, $value);
    }
}
