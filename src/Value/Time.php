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
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @param \DateInterval $interval
     *
     * @return self
     */
    public static function fromInterval(\DateInterval $interval): self
    {
        $seconds = $interval->h * 3600 + $interval->m * 60 + $interval->s;
        $micro   = $seconds * 100000 + $interval->f;

        return new self($micro * 1000);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @return \DateInterval
     */
    public function toDateInterval(): \DateInterval
    {
        $value = \floor($this->value / 1000);

        /** @noinspection PhpUnhandledExceptionInspection */
        $now = (new \DateTimeImmutable)->setTime(0, 0, 0, 0);
        $new = $now->setTime(0, 0, 0, (int) $value);

        return $now->diff($new, true);
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
