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

namespace PHPinnacle\Cassis\Response;

use PHPinnacle\Cassis\Response;

final class Supported extends Response
{
    public $opcode = self::OPCODE_SUPPORTED;
    
    /**
     * @var string[][]
     */
    public $options;
    
    /**
     * @param string[][] $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }
}
