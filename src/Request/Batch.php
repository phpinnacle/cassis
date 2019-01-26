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
use PHPinnacle\Cassis\Context;
use PHPinnacle\Cassis\Statement;

class Batch extends Frame
{
    public $opcode = self::OPCODE_QUERY;
    public $type = self::REQUEST;

    /**
     * @param int     $stream
     * @param int     $type
     * @param array   $queries
     * @param Context $context
     */
    public function __construct(int $stream, int $type, array $queries, Context $context)
    {
        $buffer = new Buffer;
        $buffer
            ->appendByte($type)
            ->appendShort(\count($queries))
        ;

        foreach ($queries as $query) {
            switch (true) {
                case $query instanceof Statement\Simple:
                    $buffer
                        ->appendByte(0)
                        ->appendLongString($query->cql())
                        ->appendValuesList($query->values())
                    ;

                    break;
                case $query instanceof Statement\Prepared:
                    $buffer
                        ->appendByte(1)
                        ->appendShortBytes($query->id())
                        ->appendValuesList($query->values())
                    ;

                    break;
            }
        }

        $buffer->appendShort($context->consistency());

        $this->stream = $stream;
        $this->body   = $buffer->flush();
    }
}
