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
use PHPinnacle\Cassis\Request;

final class Register extends Request
{
    public $opcode = self::OPCODE_REGISTER;
    
    /**
     * @var array
     */
    public $events;

    /**
     * @param array $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
    }
    
    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer->appendStringList($this->events);
    }
}
