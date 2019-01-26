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

final class UserDefined implements Type
{
    /**
     * @var string
     */
    private $keyspace;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Type[]
     */
    private $definitions;

    /**
     * @param string $keyspace
     * @param string $name
     * @param Type[] $definitions
     */
    public function __construct(string $keyspace, string $name, array $definitions)
    {
        $this->keyspace    = $keyspace;
        $this->name        = $name;
        $this->definitions = $definitions;
    }

    /**
     * @return int
     */
    public function code(): int
    {
        return self::UDT;
    }

    /**
     * @return string
     */
    public function keyspace(): string
    {
        return $this->keyspace;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Type[]
     */
    public function definitions(): array
    {
        return $this->definitions;
    }
}
