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

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Frame;

class Result extends Frame
{
    public $opcode = self::OPCODE_RESULT;
    public $type = self::RESPONSE;

    /**
     * @var int
     */
    public $kind;

    /**
     * @var Buffer
     */
    public $data;

    /**
     * @param int    $kind
     * @param Buffer $data
     */
    public function __construct(int $kind, Buffer $data)
    {
        $this->kind = $kind;
        $this->data = $data;
    }
}
