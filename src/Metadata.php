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
    const
        FLAG_GLOBAL_TABLES_SPEC = 0x0001,
        FLAG_HAS_MORE_PAGES     = 0x0002,
        FLAG_NO_METADATA        = 0x0004
    ;
    
    /**
     * @var array
     */
    private $columns;

    /**
     * @var string
     */
    private $cursor;

    /**
     * @param Column[] $columns
     * @param string   $cursor
     */
    public function __construct(array $columns, string $cursor = null)
    {
        $this->columns = $columns;
        $this->cursor  = $cursor;
    }

    /**
     * @param Buffer $buffer
     *
     * @return self
     */
    public static function create(Buffer $buffer): self
    {
        $flags  = $buffer->consumeInt();
        $count  = $buffer->consumeInt();
        $cursor = null;

        if ($flags & self::FLAG_HAS_MORE_PAGES) {
            $cursor = $buffer->consumeLongString();
        }

        if ($flags & self::FLAG_NO_METADATA) {
            return new self([], $cursor);
        }

        $columns = [];

        if ($flags & self::FLAG_GLOBAL_TABLES_SPEC) {
            $keyspace = $buffer->consumeString();
            $table    = $buffer->consumeString();

            for ($i = 0; $i < $count; ++$i) {
                $columns[] = Column::partial($keyspace, $table, $buffer);
            }
        } else {
            for ($i = 0; $i < $count; ++$i) {
                $columns[] = Column::full($buffer);
            }
        }

        return new self($columns, $cursor);
    }

    /**
     * @return string
     */
    public function cursor(): ?string
    {
        return $this->cursor;
    }

    /**
     * @return Column[]
     */
    public function columns(): array
    {
        return $this->columns;
    }
}
