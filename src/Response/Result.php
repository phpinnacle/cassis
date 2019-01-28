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

final class Result extends Response
{
    public $opcode = self::OPCODE_RESULT;

    /**
     * @var int
     */
    public $kind;

    /**
     * @var string
     */
    public $data;

    /**
     * @param int    $kind
     * @param string $data
     */
    public function __construct(int $kind, string $data)
    {
        $this->kind = $kind;
        $this->data = $data;
    }
}
