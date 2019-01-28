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

abstract class Compound implements Value
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param array  $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $tmp = new Buffer;

        foreach ($this->values as $value) {
            $tmp->appendValue($value);
        }

        $buffer
            ->appendInt($tmp->size())
            ->append($tmp->flush())
        ;
    }
}
