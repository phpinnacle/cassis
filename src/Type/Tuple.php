<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Type;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Type;
use PHPinnacle\Cassis\Value;

final class Tuple implements Type
{
    /**
     * @var Type[]
     */
    private $definitions;

    /**
     * @param Type[] $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function read(Buffer $buffer): Value
    {
        $values = [];

        foreach ($this->definitions as $key => $type) {
            $value[$key] = $type->read($buffer);
        }

        return new Value\Tuple($values);
    }
}
