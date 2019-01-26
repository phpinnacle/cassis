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

final class Parser
{
    /**
     * @var Buffer
     */
    private $buffer;

    public function __construct()
    {
        $this->buffer = new Buffer;
    }

    /**
     * @param string $chunk
     *
     * @return void
     */
    public function append(string $chunk): void
    {
        $this->buffer->append($chunk);
    }

    /**
     * @return Frame
     */
    public function parse(): ?Frame
    {
        if ($this->buffer->size() < 9) {
            return null;
        }

        $type   = $this->buffer->readByte(0);
        $flags  = $this->buffer->readByte(1);
        $stream = $this->buffer->readShort(2);
        $opcode = $this->buffer->readByte(4);
        $length = $this->buffer->readInt(5);

        if ($this->buffer->size() < $length + 9) {
            return null;
        }

        $this->buffer->discard(9);

        switch ($type) {
            case Frame::REQUEST:
                $frame = $this->parseRequest($opcode);

                break;
            case Frame::RESPONSE:
                $frame = $this->parseResponse($opcode);

                break;
            default:
                throw new Exception\ServerException;
        }

        $frame->flags  = $this->convertFlags($flags);
        $frame->stream = $stream;
        $frame->opcode = $opcode;

        return $frame;
    }

    /**
     * @param int $opcode
     *
     * @return Frame
     */
    private function parseRequest(int $opcode): Frame
    {
        switch ($opcode) {
            case Frame::OPCODE_EVENT:
                return new Request\Event;
            default:
                throw new Exception\ServerException;
        }
    }

    /**
     * @param int $opcode
     *
     * @return Frame
     */
    private function parseResponse(int $opcode): Frame
    {
        switch ($opcode) {
            case Frame::OPCODE_ERROR:
                return new Response\Error($this->buffer->consumeInt(), $this->buffer->consumeString());
            case Frame::OPCODE_READY:
                return new Response\Ready;
            case Frame::OPCODE_AUTHENTICATE:
                return new Response\Authenticate($this->buffer->consumeString());
            case Frame::OPCODE_AUTH_SUCCESS:
                return new Response\AuthSuccess;
            case Frame::OPCODE_SUPPORTED:
                return new Response\Supported;
            case Frame::OPCODE_RESULT:
                return new Response\Result($this->buffer->consumeInt(), new Buffer($this->buffer->flush()));
            default:
                throw new Exception\ServerException;
        }
    }

    /**
     * @param int $value
     *
     * @return bool[]
     */
    private function convertFlags(int $value): array
    {
        $bits = [];

        for ($i = 0; $i < 4; ++$i) {
            $bits[] = ($value & (1 << $i)) > 0;
        }

        return $bits;
    }
}
