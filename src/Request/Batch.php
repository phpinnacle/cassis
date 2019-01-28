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
use PHPinnacle\Cassis\Request;
use PHPinnacle\Cassis\Statement;

final class Batch extends Request
{
    public $opcode = self::OPCODE_BATCH;
    
    /**
     * @var int
     */
    public $type;
    
    /**
     * @var string[]
     */
    public $queries;
    
    /**
     * @var Context
     */
    public $context;
    
    /**
     * @param int      $type
     * @param string[] $queries
     * @param Context  $context
     */
    public function __construct(int $type, array $queries, Context $context)
    {
        $this->type    = $type;
        $this->queries = $queries;
        $this->context = $context;
    }
    
    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer
            ->appendByte($this->type)
            ->appendShort(\count($this->queries))
        ;

        foreach ($this->queries as $query) {
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
                        ->appendString($query->id())
                        ->appendValuesList($query->values())
                    ;

                    break;
            }
        }

        $buffer->appendShort($this->context->consistency());
    }
}
