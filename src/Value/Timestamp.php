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

final class Timestamp implements Value
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
    
    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return self
     */
    public static function fromDateTime(\DateTimeInterface $dateTime): self
    {
        return new self((int) $dateTime->format('Uu'));
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @return \DateTimeInterface
     */
    public function toDateTime(): \DateTimeInterface
    {
        $value = \substr_replace($this->value, '.', -5, 0);

        return \DateTimeImmutable::createFromFormat('U.u', $value);
    }

    /**
     * @return int
     */
    public function value(): int
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
            ->appendLong($this->value)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public static function read(Buffer $buffer): self
    {
        return new self($buffer->consumeLong());
    }
}
