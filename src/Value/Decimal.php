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

final class Decimal implements Value
{
    /**
     * @var float
     */
    private $value;

    /**
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $pos    = \strpos($this->value, '.');
        $scale  = $pos === false ? 0 : \strlen($this->value) - $pos - 1;
        $value = (($this->value * \pow(10, $scale)) & 0xffffffff00000000) >> 32;

        $buffer
            ->appendInt(8)
            ->appendUint($scale)
            ->appendUint($value)
        ;
    }

    /**
     * @param Buffer $buffer
     *
     * @return self
     */
    public static function read(Buffer $buffer): self
    {
        $scale = $buffer->consumeUint();
        $value = $buffer->consumeUint();

        return new self((float) \substr_replace($value, '.', -1 * $scale, 0));
    }
}
