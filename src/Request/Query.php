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

final class Query extends Request
{
    public $opcode = self::OPCODE_QUERY;
    
    /**
     * @var string
     */
    public $cql;
    
    /**
     * @var Context
     */
    public $context;
    
    /**
     * @param string  $cql
     * @param Context $context
     */
    public function __construct(string $cql, Context $context)
    {
        $this->cql     = $cql;
        $this->context = $context;
    }
    
    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer->appendLongString($this->cql);

        $this->context->writeParameters($buffer);
    }
}
