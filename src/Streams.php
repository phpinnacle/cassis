<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace PHPinnacle\Cassis;

final class Streams
{
    private const MAX_STREAM = 32768;

    /**
     * @var self
     */
    private static $instance;

    /**
     * @var int
     */
    private $next = 0;

    /**
     * @var \SplStack
     */
    private $stack;

    /**
     * Closed constructor
     */
    private function __construct()
    {
        $this->stack = new \SplStack;
    }

    /**
     * @return self
     */
    public static function instance(): self
    {
        return self::$instance ?: self::$instance = new self;
    }

    /**
     * @return int
     */
    public function reserve(): int
    {
        if (!$this->stack->isEmpty()) {
            return $this->stack->pop();
        }

        $next = ++$this->next;

        return $next === self::MAX_STREAM ? $this->next = 0 : $next;
    }

    /**
     * @param int $id
     */
    public function release(int $id): void
    {
        $this->stack->push($id);
    }
}
