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
use Ramsey\Uuid\UuidInterface;

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
     * @param array $bytes
     *
     * @return self
     */
    public function appendBytes(array $bytes): self
    {
        $this->appendInt(\count($bytes));

        foreach ($bytes as $byte) {
            $this->appendByte($byte);
        }

        return $this;
    }

    /**
     * @param int $n
     *
     * @return int[]
     */
    public function consumeBytes(int $n): array
    {
        return \array_values(\unpack('C*', $this->consume($n)));
    }

    /**
     * @return int[]
     */
    public function consumeBytesMap(): array
    {
        $count = $this->consumeShort();
        $bytes = [];

        for ($i = 0; $i < $count; ++$i) {
            $bytes[$this->consumeString()] = $this->consumeBytes($this->consumeInt());
        }

        return $bytes;
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function appendShort(int $value): self
    {
        $this->data->appendUint16(abs($value));

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
        $this->data->appendUint32(abs($value));

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
     * @return string[]
     */
    public function consumeStringList(): array
    {
        $values = [];
        $count  = $this->consumeShort();

        for ($i = 0; $i < $count; ++$i) {
            $values[] = $this->consumeString();
        }

        return $values;
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
                $this
                    ->appendInt(1)
                    ->appendByte($value ? 1 : 0)
                ;

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
                $this
                    ->appendInt(\strlen($value))
                    ->append($value)
                ;

                break;
            case \is_array($value):
                if (\is_assoc($value)) {
                    $list = Value\Collection::assoc($value);
                } else {
                    $list = Value\Collection::list($value);
                }

                $list->write($this);

                break;
            case $value instanceof UuidInterface:
                $this
                    ->appendInt(16)
                    ->append($value->getBytes())
                ;

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
     * @return Type
     */
    public function consumeType(): Type
    {
        $type = $this->consumeShort();

        switch ($type) {
            case Type::CUSTOM:
                return new Type\Custom($this->consumeString());
            case Type::LIST:
                return Type\Collection::list($this->consumeType());
            case Type::SET:
                return Type\Collection::set($this->consumeType());
            case Type::MAP:
                return Type\Collection::map($this->consumeType(), $this->consumeType());
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
