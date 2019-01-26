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

final class Map implements Type
{
    /**
     * @var Type
     */
    private $key;

    /**
     * @var Type
     */
    private $value;

    /**
     * @param Type $key
     * @param Type $value
     */
    public function __construct(Type $key, Type $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function code(): int
    {
        return self::COLLECTION_MAP;
    }

    /**
     * @return Type
     */
    public function key(): Type
    {
        return $this->key;
    }

    /**
     * @return Type
     */
    public function value(): Type
    {
        return $this->value;
    }
}
