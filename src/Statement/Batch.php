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
    private const
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
     * @param int         $type
     * @param Statement[] $queries
     */
    private function __construct(int $type, array $queries)
    {
        $this->type    = $type;
        $this->queries = $queries;
    }

    /**
     * @param Statement ...$queries
     *
     * @return Batch
     */
    public static function logged(Statement ...$queries): self
    {
        return new self(self::TYPE_LOGGED, $queries);
    }

    /**
     * @param Statement ...$queries
     *
     * @return Batch
     */
    public static function unlogged(Statement ...$queries): self
    {
        return new self(self::TYPE_UNLOGGED, $queries);
    }

    /**
     * @param Statement ...$queries
     *
     * @return Batch
     */
    public static function counter(Statement ...$queries): self
    {
        return new self(self::TYPE_COUNTER, $queries);
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
