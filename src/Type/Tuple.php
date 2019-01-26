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

use PHPinnacle\Cassis\Type;

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
     * @return int
     */
    public function code(): int
    {
        return self::TUPLE;
    }

    /**
     * @return Type[]
     */
    public function definitions(): array
    {
        return $this->definitions;
    }
}
