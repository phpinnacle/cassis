<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Statement;

use PHPinnacle\Cassis\Statement;

final class Batch implements Statement
{
    const
        TYPE_LOGGED   = 0,
        TYPE_UNLOGGED = 1,
        TYPE_COUNTER  = 2
    ;

    /**
     * @var int
     */
    private $type;

    /**
     * @var Statement[]
     */
    private $queries;

    /**
     * @param int          $type
     * @param Statement ...$queries
     */
    public function __construct(int $type, Statement ...$queries)
    {
        $this->type    = $type;
        $this->queries = $queries;
    }

    /**
     * @return int
     */
    public function type(): int
    {
        return $this->type;
    }

    /**
     * @return Statement[]
     */
    public function queries(): array
    {
        return $this->queries;
    }
}
