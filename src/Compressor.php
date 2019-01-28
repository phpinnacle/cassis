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

interface Compressor
{
    /**
     * @param string $data
     *
     * @return string
     */
    public function compress(string $data): string;

    /**
     * @param string $binary
     *
     * @return string
     */
    public function decompress(string $binary): string;
}
