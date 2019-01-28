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

final class Date implements Value
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
     * @param int $timestamp
     *
     * @return self
     */
    public static function fromSeconds(int $timestamp): self
    {
        return new self($timestamp - (2 ** 31));
    }

    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return self
     */
    public static function fromDateTime(\DateTimeInterface $dateTime): self
    {
        return new self((int) floor($dateTime->getTimestamp() / 86400));
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @return \DateTimeInterface
     */
    public function toDateTime(): \DateTimeInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new \DateTimeImmutable())
            ->setDate(1970, 1, $this->value + 1)
            ->setTime(0, 0, 0)
        ;
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
            ->appendInt(4)
            ->appendUint($this->value + (2 ** 31))
        ;
    }
}
