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

final class Set implements Type
{
    /**
     * @var Type
     */
    private $value;

    /**
     * @param Type $value
     */
    public function __construct(Type $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function code(): int
    {
        return self::COLLECTION_SET;
    }

    /**
     * @return Type
     */
    public function value(): Type
    {
        return $this->value;
    }
}
