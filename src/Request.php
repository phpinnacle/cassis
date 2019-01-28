<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis;

abstract class Request extends Frame
{
    public $type = self::REQUEST;

    /**
     * @param Buffer $buffer
     *
     * @return void
     */
    abstract public function write(Buffer $buffer): void;
}
