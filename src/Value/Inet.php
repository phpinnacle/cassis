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
    private function __construct(string $value)
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
        if (\filter_var($value, FILTER_VALIDATE_IP) === false) {
            throw new \InvalidArgumentException("Invalid ip address: \"{$value}\".");
        }

        return new self($value);
    }

    /**
     * @param string $bytes
     *
     * @return self
     */
    public static function fromBytes(string $bytes): self
    {
        if (!$value = @\inet_ntop($bytes)) {
            throw new \InvalidArgumentException("Cant read ip address from bytes string.");
        }

        return new self($value);
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
