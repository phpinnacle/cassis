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

namespace PHPinnacle\Cassis\Result;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Column;
use PHPinnacle\Cassis\Metadata;
use PHPinnacle\Cassis\Response;
use PHPinnacle\Cassis\Result;

class Prepared implements Result
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $keys;
    
    /**
     * @var array
     */
    private $columns;

    /**
     * @var Metadata
     */
    private $meta;

    /**
     * @param string   $id
     * @param array    $keys
     * @param array    $columns
     * @param Metadata $meta
     */
    public function __construct(string $id, array $keys, array $columns, Metadata $meta)
    {
        $this->id      = $id;
        $this->keys    = $keys;
        $this->columns = $columns;
        $this->meta    = $meta;
    }

    /**
     * @param Response\Result $frame
     *
     * @return self
     */
    public static function create(Response\Result $frame): self
    {
        $buffer = new Buffer($frame->data);
        
        $statementId  = $buffer->consumeString();
        $flags        = $buffer->consumeInt();
        $columnsCount = $buffer->consumeInt();
        $primaryCount = $buffer->consumeInt();

        $keys     = [];
        $columns  = [];
    
        for ($i = 0; $i < $primaryCount; ++$i) {
            $keys[] = $buffer->consumeShort();
        }
    
        if ($flags & Metadata::FLAG_GLOBAL_TABLES_SPEC) {
            $keyspace = $buffer->consumeString();
            $table    = $buffer->consumeString();
        
            for ($i = 0; $i < $columnsCount; ++$i) {
                $columns[] = Column::partial($keyspace, $table, $buffer);
            }
        } else {
            for ($i = 0; $i < $columnsCount; ++$i) {
                $columns[] = Column::full($buffer);
            }
        }

        return new self($statementId, $keys, $columns, Metadata::create($buffer));
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return $this->keys;
    }
    
    /**
     * @return Column[]
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * @return Metadata
     */
    public function meta(): Metadata
    {
        return $this->meta;
    }
}
