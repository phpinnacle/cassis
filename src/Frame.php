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

abstract class Frame
{
    private const
        VERSION = 0x04
    ;

    const
        REQUEST  = 0x00 + self::VERSION,
        RESPONSE = 0x80 + self::VERSION
    ;

    const
        FLAG_COMPRESSION = 0x01,
        FLAG_TRACING     = 0x02,
        FLAG_PAYLOAD     = 0x04,
        FLAG_WARNING     = 0x08
    ;

    const
        OPCODE_ERROR          = 0x00,
        OPCODE_STARTUP        = 0x01,
        OPCODE_READY          = 0x02,
        OPCODE_AUTHENTICATE   = 0x03,
        OPCODE_OPTIONS        = 0x05,
        OPCODE_SUPPORTED      = 0x06,
        OPCODE_QUERY          = 0x07,
        OPCODE_RESULT         = 0x08,
        OPCODE_PREPARE        = 0x09,
        OPCODE_EXECUTE        = 0x0A,
        OPCODE_REGISTER       = 0x0B,
        OPCODE_EVENT          = 0x0C,
        OPCODE_BATCH          = 0x0D,
        OPCODE_AUTH_CHALLENGE = 0x0E,
        OPCODE_AUTH_RESPONSE  = 0x0F,
        OPCODE_AUTH_SUCCESS   = 0x10
    ;

    /**
     * @var int
     */
    public $type;

    /**
     * @var int
     */
    public $flags = 0;

    /**
     * @var int
     */
    public $stream;

    /**
     * @var int
     */
    public $opcode;
}
