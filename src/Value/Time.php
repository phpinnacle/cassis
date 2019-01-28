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

final class Time implements Value
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    private function __construct(int $value = 0)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException("Time must be nanoseconds since midnight, {$value} given");
        }

        if ($value > 86399999999999) {
            throw new \InvalidArgumentException("Time must be nanoseconds since midnight, {$value} given");
        }

        $this->value = $value;
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public static function fromNanoSeconds(int $value): self
    {
        return new self($value);
    }
    
    /**
     * @param \DateTimeInterface $time
     *
     * @return self
     */
    public static function fromDateTime(\DateTimeInterface $time): self
    {
        $parts = \array_map('intval', \explode(':', $time->format('H:i:s:u')));

        $seconds = $parts[0] * 3600 + $parts[1] * 60 + $parts[2];
        $micro   = $seconds * 1000000 + $parts[3];

        return new self($micro * 1000);
    }

    /**
     * @param \DateInterval $interval
     *
     * @return self
     */
    public static function fromInterval(\DateInterval $interval): self
    {
        $seconds = $interval->h * 3600 + $interval->m * 60 + $interval->s;
        $micro   = $seconds * 1000000 + $interval->f;

        return new self((int) $micro * 1000);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @return \DateInterval
     */
    public function toDateInterval(): \DateInterval
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $now = (new \DateTimeImmutable)->setTime(0, 0, 0, 0);

        return $this->toDateTime()->diff($now);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @return \DateTimeInterface
     */
    public function toDateTime(): \DateTimeInterface
    {
        $value   = (int) \floor($this->value / 1000);
        $seconds = (int) \floor($value / 1000000);

        /** @noinspection PhpUnhandledExceptionInspection */
        return (new \DateTimeImmutable)->setTime(0, 0, $seconds, $value - $seconds * 1000000);
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
}
