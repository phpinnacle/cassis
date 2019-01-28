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

interface Event
{
    const
        TOPOLOGY_CHANGE = 'TOPOLOGY_CHANGE',
        STATUS_CHANGE   = 'STATUS_CHANGE',
        SCHEMA_CHANGE   = 'SCHEMA_CHANGE'
    ;

    const
        CHANGE_NODE_NEW       = 'NEW_NODE',
        CHANGE_NODE_REMOVED   = 'REMOVED_NODE',
        CHANGE_NODE_UP        = 'UP',
        CHANGE_NODE_DOWN      = 'DOWN',
        CHANGE_TARGET_CREATED = 'CREATED',
        CHANGE_TARGET_UPDATED = 'UPDATED',
        CHANGE_TARGET_DROPPED = 'DROPPED'
    ;

    const
        TARGET_KEYSPACE  = 'KEYSPACE',
        TARGET_TABLE     = 'TABLE',
        TARGET_TYPE      = 'TYPE',
        TARGET_FUNCTION  = 'FUNCTION',
        TARGET_AGGREGATE = 'AGGREGATE'
    ;

    /**
     * @return string
     */
    public function type(): string;
}
