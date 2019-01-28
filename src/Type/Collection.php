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

final class Collection implements Type
{
    const
        KIND_LIST = 0,
        KIND_SET  = 1,
        KIND_MAP  = 2
    ;

    /**
     * @var int
     */
    private $kind;

    /**
     * @var Type
     */
    private $type;

    /**
     * @param int  $kind
     * @param Type $type
     */
    private function __construct(int $kind, Type $type)
    {
        $this->kind = $kind;
        $this->type = $type;
    }

    /**
     * @param Type $type
     *
     * @return self
     */
    public static function list(Type $type): self
    {
        return new self(self::KIND_LIST, $type);
    }

    /**
     * @param Type $type
     *
     * @return self
     */
    public static function set(Type $type): self
    {
        return new self(self::KIND_SET, $type);
    }

    /**
     * @param Type $key
     * @param Type $value
     *
     * @return self
     */
    public static function map(Type $key, Type $value): self
    {
        return new self(self::KIND_MAP, new KeyValue($key, $value));
    }

    /**
     * {@inheritdoc}
     */
    public function read(Buffer $buffer): Value
    {
        $values = [];

        $slice = $buffer->slice($buffer->consumeInt());
        $count = $slice->consumeInt();

        switch ($this->kind) {
            case self::KIND_LIST:
                for ($i = 0; $i < $count; ++$i) {
                    $values[] = $this->type->read($slice);
                }

                return Value\Collection::list($values);
            case self::KIND_SET:
                for ($i = 0; $i < $count; ++$i) {
                    $values[] = $this->type->read($slice);
                }

                return Value\Collection::set($values);
            case self::KIND_MAP:
                $keys = [];

                for ($i = 0; $i < $count; ++$i) {
                    [$key, $value] = $this->type->read($slice);

                    $keys[]   = $key;
                    $values[] = $value;
                }

                return Value\Collection::map($keys, $values);
            default:
                // Newer goes here
                throw new \InvalidArgumentException;
        }
    }
}
