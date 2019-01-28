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

namespace PHPinnacle\Cassis;

interface Result
{
    const
        VOID    = 0x0001,
        ROWS    = 0x0002,
        USE     = 0x0003,
        PREPARE = 0x0004,
        ALTER   = 0x0005
    ;
}
