<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @noinspection PhpComposerExtensionStubsInspection
 */

declare(strict_types = 1);

namespace PHPinnacle\Cassis\Value;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Value;

final class Decimal implements Value
{
    /**
     * @var \GMP
     */
    private $value;

    /**
     * @var int
     */
    private $scale;
    
    /**
     * @param \GMP $value
     * @param int  $scale
     */
    public function __construct(\GMP $value, int $scale)
    {
        $this->value = $value;
        $this->scale = $scale;
    }

    /**
     * @param string $value
     * @param int    $scale
     *
     * @return self
     */
    public static function fromString(string $value, int $scale): self
    {
        return new self(\bigint_init($value), $scale);
    }

    /**
     * @param string $bytes
     * @param int    $scale
     *
     * @return self
     */
    public static function fromBytes(string $bytes, int $scale): self
    {
        return new self(\bigint_import($bytes), $scale);
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return \bigint_strval($this->value);
    }
    
    /**
     * @return int
     */
    public function scale(): int
    {
        return $this->scale;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $binary = \bigint_export($this->value);

        $buffer
            ->appendInt(4 + \strlen($binary))
            ->appendUint($this->scale)
            ->append($binary)
        ;
    }
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        $sign   = '';
        $value  = \bigint_strval($this->value);
        $length = \strlen($value);

        if ($value[0] === '-') {
            $sign  = '-';
            $value = \substr($value, 1);

            --$length;
        }

        if ($length <= $this->scale) {
            $value = \str_pad($value, $this->scale + 1, '0', \STR_PAD_LEFT);
        }

        return $sign . ($this->scale > 0 ? \substr_replace($value, '.', $this->scale * -1, 0) : $value);
    }
}
