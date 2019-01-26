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

namespace PHPinnacle\Cassis\Request;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Frame;

class AuthResponse extends Frame
{
    public $opcode = self::OPCODE_AUTH_RESPONSE;
    public $type = self::REQUEST;

    /**
     * @param string $user
     * @param string $password
     */
    public function __construct(string $user, string $password)
    {
        $buffer = new Buffer;
        $buffer
            ->appendString($user)
            ->appendString($password)
        ;

        $this->stream = 0;
        $this->body   = $buffer->flush();
    }
}
