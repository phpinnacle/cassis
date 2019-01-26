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
use PHPinnacle\Cassis\Context;
use PHPinnacle\Cassis\Frame;

class Execute extends Frame
{
    public $opcode = self::OPCODE_EXECUTE;
    public $type = self::REQUEST;

    /**
     * @param int     $stream
     * @param string  $id
     * @param array   $values
     * @param Context $context
     */
    public function __construct(int $stream, string $id, array $values, Context $context)
    {
        if (!empty($values)) {
            $context->withValues($values);
        }

        $buffer = new Buffer;
        $buffer->appendString($id);

        $this->stream = $stream;
        $this->body   = $context->queryParameters($buffer);
    }
}
