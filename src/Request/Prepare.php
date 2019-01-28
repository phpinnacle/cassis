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

final class Prepare extends Request
{
    public $opcode = self::OPCODE_PREPARE;

    /**
     * @var string
     */
    public $cql;

    /**
     * @param string $cql
     */
    public function __construct(string $cql)
    {
        $this->cql = $cql;
    }
    
    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $buffer->appendLongString($this->cql);
    }
}
