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

final class Custom implements Type
{
    /**
     * @var string
     */
    private $class;

    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function class(): string
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function read(Buffer $buffer): Value
    {
        // TODO
    }
}
