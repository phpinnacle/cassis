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

final class Metadata
{
    private const
        FLAG_GLOBAL_TABLES_SPEC = 0x0001,
        FLAG_HAS_MORE_PAGES     = 0x0002,
        FLAG_NO_METADATA        = 0x0004
    ;

    /**
     * @var string
     */
    private $paging;

    /**
     * @var array
     */
    private $columns;

    /**
     * @param string   $paging
     * @param Column[] $columns
     */
    public function __construct(string $paging = null, array $columns = [])
    {
        $this->paging  = $paging;
        $this->columns = $columns;
    }

    /**
     * @param Buffer $buffer
     *
     * @return self
     */
    public static function create(Buffer $buffer): self
    {
        $flags = $buffer->consumeInt();
        $count = $buffer->consumeInt();

        $paging   = null;
        $keyspace = null;
        $table    = null;
        $columns  = [];

        if ($flags & self::FLAG_HAS_MORE_PAGES) {
            $paging = $buffer->consumeBytes();
        }

        if ($flags & self::FLAG_NO_METADATA) {
            return new self($paging);
        }

        if ($flags & self::FLAG_GLOBAL_TABLES_SPEC) {
            $keyspace = $buffer->consumeString();
            $table    = $buffer->consumeString();

            for ($i = 0; $i < $count; ++$i) {
                $columns[] = new Column(
                    $keyspace,
                    $table,
                    $buffer->consumeString(),
                    $buffer->consumeType());
            }
        } else {
            for ($i = 0; $i < $count; ++$i) {
                $columns[] = new Column(
                    $buffer->consumeString(),
                    $buffer->consumeString(),
                    $buffer->consumeString(),
                    $buffer->consumeType()
                );
            }
        }

        return new self($paging, $columns);
    }

    /**
     * @return string
     */
    public function paging(): ?string
    {
        return $this->paging;
    }

    /**
     * @return Column[]
     */
    public function columns(): array
    {
        return $this->columns;
    }
}
