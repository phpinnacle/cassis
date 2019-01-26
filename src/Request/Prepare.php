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

class Prepare extends Frame
{
    public $opcode = self::OPCODE_PREPARE;
    public $type = self::REQUEST;

    /**
     * @param int    $stream
     * @param string $cql
     */
    public function __construct(int $stream, string $cql)
    {
        $buffer = new Buffer;
        $buffer->appendLongString($cql);

        $this->stream = $stream;
        $this->body   = $buffer->flush();
    }
}
