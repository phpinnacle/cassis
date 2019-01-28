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
use PHPinnacle\Cassis\Request;

final class AuthResponse extends Request
{
    public $opcode = self::OPCODE_AUTH_RESPONSE;

    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $password;

    /**
     * @param string $user
     * @param string $password
     */
    public function __construct(string $user, string $password)
    {
        $this->user     = $user;
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer
            ->appendString($this->user)
            ->appendString($this->password)
        ;
    }
}
