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

use function Amp\call;
use Amp\Loop;
use Amp\PHPUnit\TestCase;
use Amp\Promise;

abstract class CassisTest extends TestCase
{
    /**
     * @param callable $callable
     *
     * @return void
     */
    public static function loop(callable $callable): void
    {
        Loop::run(function () use ($callable) {
            yield call($callable);

            $info  = Loop::getInfo();
            $count = $info['enabled_watchers']['referenced'];

            if ($count !== 0) {
                $message = "Still have {$count} loop watchers.";

                foreach (['defer', 'delay', 'repeat', 'on_readable', 'on_writable'] as $key) {
                    $message .= " {$key} - {$info[$key]['enabled']}.";
                }

                self::markTestIncomplete($message);

                Loop::stop();
            }
        });
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public static function assertPromise($value): void
    {
        self::assertInstanceOf(Promise::class, $value);
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public static function assertInteger($value): void
    {
        self::assertInternalType('int', $value);
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public static function assertArray($value): void
    {
        self::assertInternalType('array', $value);
    }
}
