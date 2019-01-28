<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Tests\Compressor;

use PHPinnacle\Cassis\Tests\CassisTest;
use PHPinnacle\Cassis\Compressor;

class NoneCompressorTest extends CassisTest
{
    public function testCompressDecompress()
    {
        $compressor = new Compressor\NoneCompressor;

        self::assertEquals('abc', $compressor->compress('abc'));
        self::assertEquals('abc', $compressor->decompress('abc'));
    }
}
