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

final class Authenticate extends Response
{
    public $opcode = self::OPCODE_AUTHENTICATE;

    /**
     * @var string
     */
    public $class;

    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }
}
