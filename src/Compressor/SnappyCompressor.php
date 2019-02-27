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

namespace PHPinnacle\Cassis\Compressor;

use PHPinnacle\Cassis\Compressor;

class SnappyCompressor implements Compressor
{
    /**
     * {@inheritdoc}
     */
    public function compress(string $data): string
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        return snappy_compress($data);
    }
    
    /**
     * {@inheritdoc}
     */
    public function decompress(string $binary): string
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        return snappy_uncompress($binary);
    }
}
