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

final class Rows implements Result, \Iterator, \Countable
{
    /**
     * @var Buffer
     */
    private $data;

    /**
     * @var Metadata
     */
    private $meta;

    /**
     * @var int
     */
    private $count;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @param Buffer   $data
     * @param Metadata $meta
     * @param int      $count
     */
    private function __construct(Buffer $data, Metadata $meta, int $count)
    {
        $this->data  = $data;
        $this->meta  = $meta;
        $this->count = $count;
    }

    /**
     * @param Response\Result $frame
     *
     * @return self
     */
    public static function create(Response\Result $frame): self
    {
        $buffer = new Buffer($frame->data);
        $meta   = Metadata::create($buffer);
        $count  = $buffer->consumeInt();

        return new self($buffer, $meta, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (!isset($this->rows[$this->index])) {
            foreach ($this->meta->columns() as $column) {
                $this->rows[$this->index][$column->name()] = $column->value($this->data);
            }
        }

        return $this->rows[$this->index];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->index < $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @return string|null
     */
    public function cursor(): ?string
    {
        return $this->meta->cursor();
    }

    /**
     * @return Column[]
     */
    public function columns(): array
    {
        return $this->meta->columns();
    }
}
