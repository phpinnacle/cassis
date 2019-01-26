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

namespace PHPinnacle\Cassis\Response;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Frame;
use PHPinnacle\Cassis\Exception;

class Error extends Frame
{
    const
        SERVER         = 0x0000,
        PROTOCOL       = 0x000A,
        AUTHENTICATION = 0x0100,
        UNAVAILABLE    = 0x1000,
        OVERLOADED     = 0x1001,
        BOOTSTRAPPING  = 0x1002,
        TRUNCATING     = 0x1003,
        WRITE_TIMEOUT  = 0x1100,
        READ_TIMEOUT   = 0x1200,
        READ_FAILURE   = 0x1300,
        FUNC_FAILURE   = 0x1400,
        WRITE_FAILURE  = 0x1500,
        SYNTAX         = 0x2000,
        UNAUTHORIZED   = 0x2100,
        INVALID        = 0x2200,
        CONFIG         = 0x2300,
        ALREADY_EXISTS = 0x2400,
        UNPREPARED     = 0x2500
    ;

    public $opcode = self::OPCODE_ERROR;
    public $type = self::RESPONSE;

    /**
     * @var int
     */
    public $code;

    /**
     * @var string
     */
    public $message;

    /**
     * @param int    $code
     * @param string $message
     * @param Buffer $buffer
     */
    public function __construct(int $code, string $message, Buffer $buffer = null)
    {
        $this->code    = $code;
        $this->message = $message;

        switch ($code) {
            case self::SERVER:
            case self::PROTOCOL:
            case self::AUTHENTICATION:
            case self::UNAVAILABLE:
            case self::OVERLOADED:
            case self::BOOTSTRAPPING:
            case self::TRUNCATING:
            case self::WRITE_TIMEOUT:
            case self::READ_TIMEOUT:
            case self::READ_FAILURE:
            case self::FUNC_FAILURE:
            case self::WRITE_FAILURE:
            case self::SYNTAX:
            case self::UNAUTHORIZED:
            case self::INVALID:
            case self::CONFIG:
            case self::ALREADY_EXISTS:
            case self::UNPREPARED:
                break;
            default:
                throw new Exception\ServerException;
        }
    }
}
