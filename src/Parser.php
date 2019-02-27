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

use Ramsey\Uuid\Uuid;

final class Parser
{
    private const
        ERROR_SERVER         = 0x0000,
        ERROR_PROTOCOL       = 0x000A,
        ERROR_AUTHENTICATION = 0x0100,
        ERROR_UNAVAILABLE    = 0x1000,
        ERROR_OVERLOADED     = 0x1001,
        ERROR_BOOTSTRAPPING  = 0x1002,
        ERROR_TRUNCATING     = 0x1003,
        ERROR_WRITE_TIMEOUT  = 0x1100,
        ERROR_READ_TIMEOUT   = 0x1200,
        ERROR_READ_FAILURE   = 0x1300,
        ERROR_FUNC_FAILURE   = 0x1400,
        ERROR_WRITE_FAILURE  = 0x1500,
        ERROR_SYNTAX         = 0x2000,
        ERROR_UNAUTHORIZED   = 0x2100,
        ERROR_INVALID        = 0x2200,
        ERROR_CONFIG         = 0x2300,
        ERROR_ALREADY_EXISTS = 0x2400,
        ERROR_UNPREPARED     = 0x2500
    ;

    /**
     * @var Compressor
     */
    private $compressor;

    /**
     * @var Buffer
     */
    private $readBuffer;
    
    /**
     * @var Buffer
     */
    private $frameBuffer;
    
    /**
     * @param Compressor $compressor
     */
    public function __construct(Compressor $compressor)
    {
        $this->compressor  = $compressor;
        $this->readBuffer  = new Buffer;
        $this->frameBuffer = new Buffer;
    }

    /**
     * @param string $chunk
     *
     * @return void
     */
    public function append(string $chunk): void
    {
        $this->readBuffer->append($chunk);
    }

    /**
     * @return Frame
     */
    public function parse(): ?Frame
    {
        if ($this->readBuffer->size() < 9) {
            return null;
        }

        $type   = $this->readBuffer->readByte(0);
        $flags  = $this->readBuffer->readByte(1);
        $stream = $this->readBuffer->readSmallInt(2);
        $opcode = $this->readBuffer->readByte(4);
        $length = $this->readBuffer->readInt(5);

        if ($type !== Frame::RESPONSE) {
            throw new Exception\ServerException;
        }

        if ($this->readBuffer->size() < $length + 9) {
            return null;
        }

        $this->readBuffer->discard(9);

        $body = $this->readBuffer->consume($length);

        if ($flags & Frame::FLAG_COMPRESSION) {
            $body = $this->compressor->decompress($body);
        }

        $this->frameBuffer->append($body);

        if ($flags & Frame::FLAG_TRACING) {
            $tracing = Uuid::fromBytes($this->frameBuffer->consume(16));
        }

        if ($flags & Frame::FLAG_PAYLOAD) {
            $payload = $this->frameBuffer->consumeBytesMap();
        }

        if ($flags & Frame::FLAG_WARNING) {
            $warnings = $this->frameBuffer->consumeStringList();
        }

        $frame         = $this->frame($opcode, $length);
        $frame->flags  = $flags;
        $frame->stream = $stream;
        $frame->opcode = $opcode;

        return $frame;
    }
    
    /**
     * @param int $opcode
     * @param int $length
     *
     * @return Frame
     */
    private function frame(int $opcode, int $length): Frame
    {
        switch ($opcode) {
            case Frame::OPCODE_READY:
                return new Response\Ready;
            case Frame::OPCODE_AUTHENTICATE:
                return new Response\Authenticate($this->frameBuffer->consumeString());
            case Frame::OPCODE_AUTH_SUCCESS:
                return new Response\AuthSuccess($this->frameBuffer->consume($length));
            case Frame::OPCODE_RESULT:
                return new Response\Result($this->frameBuffer->consumeInt(), $this->frameBuffer->consume($length - 4));
            case Frame::OPCODE_ERROR:
                return $this->parseError($this->frameBuffer->consumeInt(), $this->frameBuffer->consumeString());
            case Frame::OPCODE_SUPPORTED:
                return $this->parseSupported($this->frameBuffer->consumeShort());
            case Frame::OPCODE_EVENT:
                return $this->parseEvent($this->frameBuffer->consumeString());
            default:
                throw new Exception\ServerException;
        }
    }

    /**
     * @param int    $code
     * @param string $message
     *
     * @return Frame
     */
    private function parseError(int $code, string $message): Frame
    {
        switch ($code) {
            case self::ERROR_SERVER:
                $exception = new Exception\ServerException($message);

                break;
            case self::ERROR_PROTOCOL:
                $exception = new Exception\ProtocolException($message);

                break;
            case self::ERROR_AUTHENTICATION:
                $exception = new Exception\AuthenticationException($message);

                break;
            case self::ERROR_UNAVAILABLE:
                $exception = new Exception\UnavailableException(
                    $message,
                    $this->frameBuffer->consumeShort(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeInt()
                );

                break;
            case self::ERROR_OVERLOADED:
                $exception = new Exception\OverloadedException($message);

                break;
            case self::ERROR_BOOTSTRAPPING:
                $exception = new Exception\BootstrappingException($message);

                break;
            case self::ERROR_TRUNCATING:
                $exception = new Exception\TruncatingException($message);

                break;
            case self::ERROR_WRITE_TIMEOUT:
                $exception = new Exception\WriteTimeoutException(
                    $message,
                    $this->frameBuffer->consumeShort(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeString()
                );

                break;
            case self::ERROR_READ_TIMEOUT:
                $exception = new Exception\ReadTimeoutException(
                    $message,
                    $this->frameBuffer->consumeShort(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeByte() > 0
                );

                break;
            case self::ERROR_READ_FAILURE:
                $exception = new Exception\ReadFailureException(
                    $message,
                    $this->frameBuffer->consumeShort(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeByte() > 0
                );

                break;
            case self::ERROR_FUNC_FAILURE:
                $exception = new Exception\FunctionFailureException(
                    $message,
                    $this->frameBuffer->consumeString(),
                    $this->frameBuffer->consumeString(),
                    $this->frameBuffer->consumeStringList()
                );

                break;
            case self::ERROR_WRITE_FAILURE:
                $exception = new Exception\WriteFailureException(
                    $message,
                    $this->frameBuffer->consumeShort(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeInt(),
                    $this->frameBuffer->consumeString()
                );

                break;
            case self::ERROR_SYNTAX:
                $exception = new Exception\SyntaxException($message);

                break;
            case self::ERROR_UNAUTHORIZED:
                $exception = new Exception\UnauthorizedException($message);

                break;
            case self::ERROR_INVALID:
                $exception = new Exception\InvalidQueryException($message);

                break;
            case self::ERROR_CONFIG:
                $exception = new Exception\ConfigException($message);

                break;
            case self::ERROR_ALREADY_EXISTS:
                $exception = new Exception\AlreadyExistsException(
                    $message,
                    $this->frameBuffer->consumeString(),
                    $this->frameBuffer->consumeString()
                );

                break;
            case self::ERROR_UNPREPARED:
                $count = $this->frameBuffer->consumeShort();

                $exception = new Exception\UnpreparedException(
                    $message,
                    $this->frameBuffer->consume($count)
                );

                break;
            default:
                throw new Exception\ServerException;
        }

        return new Response\Error($exception);
    }

    /**
     * @param int $size
     *
     * @return Frame
     */
    private function parseSupported(int $size): Frame
    {
        $options = [];

        for ($i = 0; $i < $size; ++$i) {
            $option = \strtoupper($this->frameBuffer->consumeString());
            $length = $this->frameBuffer->consumeShort();

            for ($j = 0; $j < $length; ++$j) {
                $options[$option][] = $this->frameBuffer->consumeString();
            }
        }

        return new Response\Supported($options);
    }

    /**
     * @param string $type
     *
     * @return Frame
     */
    private function parseEvent(string $type): Frame
    {
        $change = $this->frameBuffer->consumeString();

        switch ($type) {
            case Event::TOPOLOGY_CHANGE:
                $length  = $this->frameBuffer->consumeInt();
                $address = $this->frameBuffer->consume($length);

                $event = new Event\TopologyChange($change, $address);

                break;
            case Event::STATUS_CHANGE:
                $length  = $this->frameBuffer->consumeInt();
                $address = $this->frameBuffer->consume($length);

                $event = new Event\StatusChange($change, $address);

                break;
            case Event::SCHEMA_CHANGE:
                $target = $this->frameBuffer->consumeString();

                switch ($target) {
                    case Event::TARGET_KEYSPACE:
                        $keyspace = $this->frameBuffer->consumeString();

                        $event = new Event\SchemaChange($change, $target, $keyspace);

                        break;
                    case Event::TARGET_TABLE:
                    case Event::TARGET_TYPE:
                        $keyspace = $this->frameBuffer->consumeString();
                        $name     = $this->frameBuffer->consumeString();

                        $event = new Event\SchemaChange($change, $target, $keyspace, $name);

                        break;
                    case Event::TARGET_FUNCTION:
                    case Event::TARGET_AGGREGATE:
                        $keyspace  = $this->frameBuffer->consumeString();
                        $name      = $this->frameBuffer->consumeString();
                        $arguments = $this->frameBuffer->consumeStringList();

                        $event = new Event\SchemaChange($change, $target, $keyspace, $name, $arguments);

                        break;
                    default:
                        throw new Exception\ServerException;
                }

                break;
            default:
                throw new Exception\ServerException;
        }

        return new Response\Event($event);
    }
}
