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

final class Rows implements Result, \Iterator, \Countable, \ArrayAccess
{
    /**
     * @var \SplFixedArray
     */
    public $data;

    /**
     * @var Metadata
     */
    private $meta;
    
    /**
     * @param \SplFixedArray $data
     * @param Metadata       $meta
     */
    public function __construct(\SplFixedArray $data, Metadata $meta)
    {
        $this->data = $data;
        $this->meta = $meta;
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

        $count = $buffer->consumeInt();
        $rows  = new \SplFixedArray($count);

        for ($i = 0; $i < $count; ++$i) {
            $data = [];

            foreach ($meta->columns() as $column) {
                $data[$column->name()] = $column->value($buffer);
            }

            $rows[$i] = $data;
        }

        return new self($rows, $meta);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->data->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->data->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->data->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->data->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->data->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->data->count();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->data->offsetExists($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->data->offsetGet($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Rows result are immutable.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Rows result are immutable.');
    }

    /**
     * @return Metadata
     */
    public function meta(): Metadata
    {
        return $this->meta;
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
