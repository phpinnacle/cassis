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

final class Double implements Value
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
     * @param float $value
     *
     * @return self
     */
    public static function fromFloat(float $value): self
    {
        return new self($value);
    }
    
    /**
     * @param string $value
     *
     * @return self
     */
    public static function fromString(string $value): self
    {
        if (false === \is_numeric($value)) {
            throw new \InvalidArgumentException("Value \"{$value}\" not numeric");
        }
        
        return new self((float) $value);
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
        $buffer
            ->appendInt(8)
            ->appendDouble($this->value)
        ;
    }
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
