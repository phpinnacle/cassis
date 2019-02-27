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

namespace PHPinnacle\Cassis\Exception;

class ServerException extends CassisException
{
    public static function unexpectedFrame(int $code): self
    {
        return new self("Unexpected frame with opcode {$code}.");
    }
}
