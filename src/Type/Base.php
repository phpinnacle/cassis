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
use PHPinnacle\Cassis\Exception;
use PHPinnacle\Cassis\Type;
use PHPinnacle\Cassis\Value;
use Ramsey\Uuid\Uuid;

final class Base implements Type
{
    /**
     * @var int
     */
    private $code;

    /**
     * @param int $code
     */
    public function __construct(int $code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function read(Buffer $buffer)
    {
        $length = $buffer->consumeInt();

        if ($length < 0) {
            return null;
        }

        switch ($this->code) {
            case Type::ASCII:
            case Type::VARCHAR:
            case Type::TEXT:
                return $buffer->consume($length);
            case Type::BLOB:
                return Value\Blob::fromArray($buffer->consumeBytes($length));
            case Type::BOOLEAN:
                return (bool) $buffer->consumeByte();
            case Type::TINYINT:
                return $buffer->consumeTinyInt();
            case Type::SMALLINT:
                return $buffer->consumeSmallInt();
            case Type::INT:
                return $buffer->consumeInt();
            case Type::BIGINT:
                return $buffer->consumeLong();
            case Type::VARINT:
                return Value\Varint::fromBytes($buffer->consume($length));
            case Type::FLOAT:
                return $buffer->consumeFloat();
            case Type::DOUBLE:
                return $buffer->consumeDouble();
            case Type::DECIMAL:
                $scale = $buffer->consumeUint();
                $bytes = $buffer->consume($length - 4);

                return Value\Decimal::fromBytes($bytes, $scale);
            case Type::TIMESTAMP:
                return Value\Timestamp::fromMicroSeconds($buffer->consumeLong());
            case Type::DATE:
                return Value\Date::fromSeconds($buffer->consumeUint());
            case Type::TIME:
                return Value\Time::fromNanoSeconds($buffer->consumeLong());
            case Type::UUID:
            case Type::TIMEUUID:
                return Uuid::fromBytes($buffer->consume(16));
            case Type::INET:
                return Value\Inet::fromBytes($buffer->consume($length));
            case Type::COUNTER:
                return new Value\Counter($buffer->consumeLong());
            default:
                throw Exception\ClientException::unknownType($this->code);
        }
    }
}
