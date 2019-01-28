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

final class ClientException extends CassisException
{
    public static function alreadyConnected(): self
    {
        return new self("Client already connected/connecting.");
    }

    public static function unknownType(int $code): self
    {
        return new self("Unknown type with code {$code}.");
    }

    public static function couldNotConnect(): self
    {
        return new self("Could not connect to any providing host.");
    }
}
