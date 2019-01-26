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

class Startup extends Frame
{
    public $opcode = self::OPCODE_STARTUP;
    public $type = self::REQUEST;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $buffer = new Buffer;
        $buffer->appendStringMap($options);

        $this->stream = 0;
        $this->body   = $buffer->flush();
    }
}
