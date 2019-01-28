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

final class Varint implements Value
{
    /**
     * @var \GMP
     */
    private $value;

    /**
     * @param \GMP $value
     */
    public function __construct(\GMP $value)
    {
        $this->value = $value;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self(bigint_init($value));
    }

    /**
     * @param string $bytes
     *
     * @return self
     */
    public static function fromBytes(string $bytes): self
    {
        return new self(bigint_import($bytes));
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $binary = gmp_export($this->value);

        $buffer
            ->appendInt(\strlen($binary))
            ->append($binary)
        ;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \gmp_strval($this->value);
    }
}
