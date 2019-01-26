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

interface Type
{
    const
        CUSTOM          = 0x0000,
        ASCII           = 0x0001,
        BIGINT          = 0x0002,
        BLOB            = 0x0003,
        BOOLEAN         = 0x0004,
        COUNTER         = 0x0005,
        DECIMAL         = 0x0006,
        DOUBLE          = 0x0007,
        FLOAT           = 0x0008,
        INT             = 0x0009,
        TEXT            = 0x000A,
        TIMESTAMP       = 0x000B,
        UUID            = 0x000C,
        VARCHAR         = 0x000D,
        VARINT          = 0x000E,
        TIMEUUID        = 0x000F,
        INET            = 0x0010,
        DATE            = 0x0011,
        TIME            = 0x0012,
        SMALLINT        = 0x0013,
        TINYINT         = 0x0014,
        COLLECTION_LIST = 0x0020,
        COLLECTION_MAP  = 0x0021,
        COLLECTION_SET  = 0x0022,
        UDT             = 0x0030,
        TUPLE           = 0x0031
    ;

    /**
     * @return int
     */
    public function code(): int;
}
