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

final class Execute extends Request
{
    public $opcode = self::OPCODE_EXECUTE;

    /**
     * @var string
     */
    public $id;
    
    /**
     * @var Context
     */
    public $context;
    
    /**
     * @param string  $id
     * @param Context $context
     */
    public function __construct(string $id, Context $context)
    {
        $this->id      = $id;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer->appendString($this->id);
        
        $this->context->writeParameters($buffer);
    }
}
