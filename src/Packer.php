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

final class Packer
{
    /**
     * @var Compressor
     */
    private $compressor;

    /**
     * @var Buffer
     */
    private $writeBuffer;

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
        $this->writeBuffer = new Buffer;
        $this->frameBuffer = new Buffer;
    }
    
    /**
     * @param Request $frame
     * @param int     $stream
     *
     * @return string
     */
    public function pack(Request $frame, int $stream): string
    {
        $frame->write($this->frameBuffer);

        $body = $this->frameBuffer->flush();

        $this->writeBuffer
            ->appendByte($frame->type)
            ->appendByte($frame->flags)
            ->appendShort($stream)
            ->appendByte($frame->opcode)
            ->appendLongString($this->compressor->compress($body))
        ;

        return $this->writeBuffer->flush();
    }
}
