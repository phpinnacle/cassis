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

use PHPinnacle\Cassis\Metadata;
use PHPinnacle\Cassis\Response;
use PHPinnacle\Cassis\Result;

final class Rows implements Result, \IteratorAggregate, \Countable
{
    /**
     * @var \SplFixedArray
     */
    public $data;

    /**
     * @param \SplFixedArray $data
     */
    public function __construct(\SplFixedArray $data)
    {
        $this->data = $data;
    }

    /**
     * @param Response\Result $frame
     *
     * @return self
     */
    public static function create(Response\Result $frame): self
    {
        $buffer   = $frame->data;
        $metadata = Metadata::create($buffer);

        $count = $buffer->consumeInt();
        $rows  = new \SplFixedArray($count);

        for ($i = 0; $i < $count; ++$i) {
            $data = [];

            foreach ($metadata->columns() as $column) {
                $data[$column->name()] = $buffer->consumeValue($column->type());
            }

            $rows[$i] = $data;
        }

        return new self($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->data->count();
    }
}
