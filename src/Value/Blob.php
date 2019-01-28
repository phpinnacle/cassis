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

final class Blob implements Value
{
    /**
     * @var array
     */
    private $values;
    
    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param array $bytes
     *
     * @return self
     */
    public static function fromArray(array $bytes): self
    {
        return new self($bytes);
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public static function fromString(string $value): self
    {
        $length = \strlen($value);
        $values = [];

        for ($i = 0; $i < $length; ++$i) {
            $values[] = \ord($value[$i]);
        }

        return new self($values);
    }
    
    /**
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }
    
    /**
     * @return string
     */
    public function bytes(): string
    {
        return '0x' . implode('', array_map('dechex', $this->values));
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer->appendInt(\count($this->values));
        
        foreach ($this->values as $value) {
            $buffer->appendByte($value);
        }
    }
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return \implode('', \array_map(function (int $value) {
            return \chr($value);
        }, $this->values));
    }
}
