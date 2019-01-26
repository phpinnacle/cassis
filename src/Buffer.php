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

namespace PHPinnacle\Cassis;

use PHPinnacle\Buffer\Binary;

final class Buffer
{
    /**
     * @var Binary
     */
    private $data;

    /**
     * @param string $initial
     */
    public function __construct(string $initial = '')
    {
        $this->data = new Binary($initial);
    }

    /**
     * @param $data
     *
     * @return self
     */
    public function append($data): self
    {
        $this->data->append($data);
        
        return $this;
    }

    /**
     * @param int $n
     *
     * @return string
     */
    public function consume(int $n): string
    {
        return $this->data->consume($n);
    }

    /**
     * @param int $n
     *
     * @return self
     */
    public function slice(int $n): self
    {
        return new self($this->consume($n));
    }

    /**
     * @param int $n
     *
     * @return self
     */
    public function discard(int $n): self
    {
        $this->data->discard($n);

        return $this;
    }

    /**
     * @return string
     */
    public function flush(): string
    {
        return $this->data->flush();
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return $this->data->size();
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendByte(int $value): self
    {
        $this->data->appendUint8($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function readByte(int $offset = 0): int
    {
        return $this->data->readUint8($offset);
    }

    /**
     * @return int
     */
    public function consumeByte(): int
    {
        return $this->data->consumeUint8();
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendShort(int $value): self
    {
        $this->data->appendUint16($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function readShort(int $offset = 0): int
    {
        return $this->data->readUint16($offset);
    }

    /**
     * @return int
     */
    public function consumeShort(): int
    {
        return $this->data->consumeUint16();
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendTinyInt(int $value): self
    {
        $this->data->appendInt8($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function readTinyInt(int $offset = 0): int
    {
        return $this->data->readInt8($offset);
    }

    /**
     * @return int
     */
    public function consumeTinyInt(): int
    {
        return $this->data->consumeInt8();
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendSmallInt(int $value): self
    {
        $this->data->appendInt16($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function readSmallInt(int $offset = 0): int
    {
        return $this->data->readInt16($offset);
    }

    /**
     * @return int
     */
    public function consumeSmallInt(): int
    {
        return $this->data->consumeInt16();
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendInt(int $value): self
    {
        $this->data->appendInt32($value);
        
        return $this;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function readInt(int $offset = 0): int
    {
        return $this->data->readInt32($offset);
    }

    /**
     * @return int
     */
    public function consumeInt(): int
    {
        return $this->data->consumeInt32();
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendUint(int $value): self
    {
        $this->data->appendUint32($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function readUint(int $offset = 0): int
    {
        return $this->data->readUint32($offset);
    }

    /**
     * @return int
     */
    public function consumeUint(): int
    {
        return $this->data->consumeUint32();
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendLong(int $value): self
    {
        $this->data->appendInt64($value);
        
        return $this;
    }

    /**
     * @param int $offset
     *
     * @return int
     */
    public function readLong(int $offset = 0): int
    {
        return $this->data->readInt64($offset);
    }

    /**
     * @return int
     */
    public function consumeLong(): int
    {
        return $this->data->consumeInt64();
    }

    /**
     * @param float $value
     *
     * @return self
     */
    public function appendFloat(float $value): self
    {
        $this->data->appendFloat($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return float
     */
    public function readFloat(int $offset = 0): float
    {
        return $this->data->readFloat($offset);
    }

    /**
     * @return float
     */
    public function consumeFloat(): float
    {
        return $this->data->consumeFloat();
    }

    /**
     * @param float $value
     *
     * @return self
     */
    public function appendDouble(float $value): self
    {
        $this->data->appendDouble($value);

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return float
     */
    public function readDouble(int $offset = 0): float
    {
        return $this->data->readDouble($offset);
    }

    /**
     * @return float
     */
    public function consumeDouble(): float
    {
        return $this->data->consumeDouble();
    }
    
    /**
     * @param string $value
     *
     * @return self
     */
    public function appendString(string $value): self
    {
        return $this
            ->appendShort(\strlen($value))
            ->append($value)
        ;
    }

    /**
     * @param int $offset
     *
     * @return string
     */
    public function readString(int $offset = 0): string
    {
        return $this->data->read($this->readShort($offset), $offset + 2);
    }

    /**
     * @return string
     */
    public function consumeString(): string
    {
        return $this->data->consume($this->consumeShort());
    }
    
    /**
     * @param string $value
     *
     * @return self
     */
    public function appendLongString(string $value): self
    {
        return $this
            ->appendInt(\strlen($value))
            ->append($value)
        ;
    }

    /**
     * @param int $offset
     *
     * @return string
     */
    public function readLongString(int $offset = 0): string
    {
        return $this->data->read($this->readInt($offset), $offset + 4);
    }

    /**
     * @return string
     */
    public function consumeLongString(): string
    {
        return $this->data->consume($this->consumeInt());
    }

    /**
     * @param array $values
     *
     * @return self
     */
    public function appendStringList(array $values): self
    {
        $this->appendShort(\count($values));
        
        foreach ($values as $value) {
            $this->appendString($value);
        }

        return $this;
    }
    
    /**
     * @param array $values
     *
     * @return self
     */
    public function appendStringMap(array $values): self
    {
        $this->appendShort(\count($values));
        
        foreach ($values as $key => $value) {
            $this->appendString($key);
            $this->appendString($value);
        }
        
        return $this;
    }
    
    /**
     * @param array $values
     *
     * @return self
     */
    public function appendStringMultiMap(array $values): self
    {
        $this->appendShort(\count($values));
        
        foreach ($values as $key => $value) {
            $this->appendString($key);
            $this->appendStringList($value);
        }
        
        return $this;
    }
    
    /**
     * @param int   $id
     * @param mixed $value
     *
     * @return self
     */
    public function appendOption(int $id, $value): self
    {
        return $this
            ->appendShort($id)
            ->append($value)
        ;
    }
    
    /**
     * @param string[] $values
     *
     * @return self
     */
    public function appendOptionList(array $values): self
    {
        $this->appendShort(\count($values));
    
        foreach ($values as $id => $value) {
            $this->appendOption($id, $value);
        }
        
        return $this;
    }
    
    /**
     * @param string $value
     *
     * @return self
     */
    public function appendBytes(string $value): self
    {
        return $this
            ->appendInt(\strlen($value))
            ->append($value)
        ;
    }

    /**
     * @param int $offset
     *
     * @return string
     */
    public function readBytes(int $offset = 0): string
    {
        return $this->data->read($this->readInt($offset), $offset + 4);
    }

    /**
     * @return string
     */
    public function consumeBytes(): ?string
    {
        $n = $this->consumeInt();

        return $n < 0 ? null : $this->data->consume($n);
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function appendShortBytes(string $value): self
    {
        return $this
            ->appendShort(\strlen($value))
            ->append($value)
        ;
    }

    /**
     * @param int $offset
     *
     * @return string
     */
    public function readShortBytes(int $offset = 0): string
    {
        return $this->data->read($this->readShort($offset), $offset + 2);
    }

    /**
     * @return string
     */
    public function consumeShortBytes(): string
    {
        return $this->data->consume($this->consumeShort());
    }

    /**
     * @param array $values
     *
     * @return self
     */
    public function appendBytesMap(array $values): self
    {
        $this->appendShort(\count($values));
    
        foreach ($values as $key => $value) {
            $this->appendString($key);
            $this->appendBytes($value);
        }

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function appendValue($value): self
    {
        switch (true) {
            case $value instanceof Value:
                $value->write($this);

                break;
            case \is_null($value):
                $this->append("\xff\xff\xff\xff");

                break;
            case \is_bool($value):
                $this->appendByte($value ? 1 : 0);

                break;
            case \is_int($value):
                $this
                    ->appendInt(4)
                    ->appendInt($value)
                ;

                break;
            case \is_float($value):
                $this
                    ->appendInt(4)
                    ->appendFloat($value)
                ;

                break;
            case \is_string($value):
                $this->appendLongString($value);

                break;
            case $value instanceof \DateTimeInterface:
                $this
                    ->appendInt(8)
                    ->appendLong((int) $value->format('Uu'))
                ;

                break;
            default:
                throw new Exception\ClientException('Unknown type.');
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return self
     */
    public function appendValuesList(array $values): self
    {
        $this->appendShort(\count($values));

        foreach ($values as $value) {
            $this->appendValue($value);
        }

        return $this;
    }

    /**
     * @param array $values
     *
     * @return self
     */
    public function appendValuesMap(array $values): self
    {
        $this->appendShort(\count($values));

        foreach ($values as $name => $value) {
            $this->appendString(\strtolower($name));
            $this->appendValue($value);
        }

        return $this;
    }

    /**
     * @param Type $type
     *
     * @return null|bool|float|int|string|Value
     */
    public function consumeValue(Type $type)
    {
        $value  = null;
        $length = $this->consumeInt();

        if ($length < 0) {
            return $value;
        }

        switch ($type->code()) {
            case Type::CUSTOM:
                $value = $this->consumeString();
                break;
            case Type::ASCII:
            case Type::VARCHAR:
            case Type::TEXT:
            case Type::BLOB:
                $value = $this->consume($length);
                break;
            case Type::BOOLEAN:
                $value = (bool) $this->consumeByte();
                break;
            case Type::SMALLINT:
                $value = $this->consumeSmallInt();
                break;
            case Type::TINYINT:
                $value = $this->consumeTinyInt();
                break;
            case Type::INT:
                $value = $this->consumeInt();
                break;
            case Type::BIGINT:
                $value = $this->consumeLong();
                break;
            case Type::FLOAT:
                $value = $this->consumeFloat();
                break;
            case Type::DOUBLE:
                $value = Value\Double::read($this)->value();
                break;
            case Type::DECIMAL:
                $value = Value\Decimal::read($this)->value();
                break;
            case Type::DATE:
                $value = Value\Date::read($this);
                break;
            case Type::TIME:
                $value = Value\Time::read($this);
                break;
            case Type::TIMESTAMP:
                $value = Value\Timestamp::read($this);
                break;
            case Type::INET:
                $value = $length === 4 ? Value\Inet::readV4($this) : Value\Inet::readV6($this);
                break;
            case Type::UUID:
                $value = Value\Uuid::read($this);
                break;
            case Type::COUNTER:
                $value = Value\Counter::read($this);
                break;
            case Type::TIMEUUID:
                $value = Value\Timeuuid::read($this);
                break;
            case Type::COLLECTION_LIST:
            case Type::COLLECTION_SET:
                $value = [];
                $count = $this->consumeInt();

                for ($i = 0; $i < $count; ++$i) {
                    /** @var Type\Collection|Type\Set $type */
                    $value[] = $this->consumeValue($type->value());
                }

                break;
            case Type::COLLECTION_MAP:
                $value = [];
                $count = $this->consumeInt();

                /** @var Type\Map $type */
                [$keyType, $valueType] = [$type->key(), $type->value()];

                for ($i = 0; $i < $count; ++$i) {
                    $value[$this->consumeValue($keyType)] = $this->consumeValue($valueType);
                }

                break;
            case Type::UDT:
            case Type::TUPLE:
                $value = [];

                /** @var Type\Tuple|Type\UserDefined $type */
                foreach ($type->definitions() as $key => $type) {
                    $value[$key] = $this->consumeValue($type);
                }

                break;
            default:
                throw new Exception\ClientException('Unknown type.');
        }

        return $value;
    }

    /**
     * @return Type
     */
    public function consumeType(): Type
    {
        $type = $this->consumeShort();

        switch ($type) {
            case Type::CUSTOM:
                return new Type\Custom($this->consumeString());
            case Type::COLLECTION_LIST:
                return new Type\Collection($this->consumeType());
            case Type::COLLECTION_SET:
                return new Type\Set($this->consumeType());
            case Type::COLLECTION_MAP:
                return new Type\Map($this->consumeType(), $this->consumeType());
            case Type::UDT:
                $keyspace = $this->consumeString();
                $name     = $this->consumeString();
                $length   = $this->consumeShort();

                $definitions = [];

                for($i = 0; $i < $length; ++$i) {
                    $definitions[$this->consumeString()] = $this->consumeType();
                }

                return new Type\UserDefined($keyspace, $name, $definitions);
            case Type::TUPLE:
                $length = $this->consumeShort();

                $definitions = [];

                for($i = 0; $i < $length; ++$i) {
                    $definitions[] = $this->consumeType();
                }

                return new Type\Tuple($definitions);
            default:
                return new Type\Base($type);
        }
    }
}
