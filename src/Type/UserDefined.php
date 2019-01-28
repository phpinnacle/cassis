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
     * {@inheritdoc}
     */
    public function read(Buffer $buffer): Value
    {
        $values = [];
        $slice  = $buffer->slice($buffer->consumeInt());

        foreach ($this->definitions as $key => $type) {
            $values[$key] = $type->read($slice);
        }

        return new Value\UserDefined($values);
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
}
