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

final class Integer implements Value
{
    /**
     * @var int
     */
    private $value;

    /**
     * @var int
     */
    private $size;

    /**
     * @param int $value
     * @param int $size
     */
    public function __construct(int $value, int $size = 4)
    {
        $this->value = $value;
        $this->size  = $size;
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public static function tiny(int $value): self
    {
        return new self($value, 1);
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public static function small(int $value): self
    {
        return new self($value, 2);
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public static function big(int $value): self
    {
        return new self($value, 8);
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer->appendInt($this->size);

        switch ($this->size) {
            case 1:
                $buffer->appendTinyInt($this->value);

                break;
            case 2:
                $buffer->appendSmallInt($this->value);

                break;
            case 4:
                $buffer->appendInt($this->value);

                break;
            case 8:
                $buffer->appendLong($this->value);

                break;
        }
    }
}
