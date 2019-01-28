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

final class ConfigException extends CassisException
{
    public static function unknownCompressionMechanism(string $compression): self
    {
        return new self("Unknown compression mechanism \"{$compression}\".");
    }

    public static function compressionExtensionNotLoaded(string $compression): self
    {
        return new self("Extension for compression mechanism \"{$compression}\" not loaded.");
    }
}
