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
use Ramsey\Uuid\Uuid as Implementation;

class Uuid implements Value
{
    /**
     * @var Implementation
     */
    protected $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = Implementation::fromString($value);
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer
            ->appendInt(16)
            ->append($this->value->getBytes())
        ;
    }

    /**
     * @param Buffer $buffer
     *
     * @return static
     */
    public static function read(Buffer $buffer): self
    {
        return new static(Implementation::fromBytes($buffer->consume(16))->toString());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value->toString();
    }
}
