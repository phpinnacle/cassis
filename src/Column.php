<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis;

final class Column
{
    /**
     * @var string
     */
    private $keyspace;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $type;

    /**
     * @param string $keyspace
     * @param string $table
     * @param string $name
     * @param Type   $type
     */
    public function __construct(string $keyspace, string $table, string $name, Type $type)
    {
        $this->keyspace = $keyspace;
        $this->table    = $table;
        $this->name     = $name;
        $this->type     = $type;
    }

    /**
     * @param Buffer $buffer
     *
     * @return self
     */
    public static function full(Buffer $buffer): self
    {
        return new self(
            $buffer->consumeString(),
            $buffer->consumeString(),
            $buffer->consumeString(),
            $buffer->consumeType()
        );
    }

    /**
     * @param string $keyspace
     * @param string $table
     * @param Buffer $buffer
     *
     * @return self
     */
    public static function partial(string $keyspace, string $table, Buffer $buffer): self
    {
        return new self($keyspace, $table, $buffer->consumeString(), $buffer->consumeType());
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
    public function table(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Type
     */
    public function type(): Type
    {
        return $this->type;
    }

    /**
     * @param Buffer $buffer
     *
     * @return mixed
     */
    public function value(Buffer $buffer)
    {
        return $this->type->read($buffer);
    }
}
