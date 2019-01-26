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

namespace PHPinnacle\Cassis\Value;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Value;

final class Inet implements Value
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public static function readV4(Buffer $buffer): self
    {
        return new self(\inet_ntop($buffer->consume(4)));
    }

    /**
     * {@inheritdoc}
     */
    public static function readV6(Buffer $buffer): self
    {
        return new self(\inet_ntop($buffer->consume(16)));
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $bytes = \inet_pton($this->value);

        $buffer
            ->appendInt(\strlen($bytes))
            ->append($bytes)
        ;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
