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

namespace PHPinnacle\Cassis\Value;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Value;
use PHPinnacle\Cassis\Type;

final class Collection implements Value, \Iterator, \Countable, \ArrayAccess
{
    /**
     * @var int
     */
    private $kind;

    /**
     * @var array
     */
    private $keys;

    /**
     * @var array
     */
    private $values;

    /**
     * @param int   $kind
     * @param array $keys
     * @param array $values
     */
    private function __construct(int $kind, array $keys, array $values)
    {
        $this->kind   = $kind;
        $this->keys   = $keys;
        $this->values = $values;
    }

    /**
     * @param array $values
     *
     * @return self
     */
    public static function list(array $values): self
    {
        $values = \array_values($values);

        return new self(Type\Collection::KIND_LIST, \array_keys($values), $values);
    }

    /**
     * @param array $values
     *
     * @return self
     */
    public static function set(array $values): self
    {
        $values = \array_unique($values);

        return new self(Type\Collection::KIND_SET, \array_keys($values), \array_values($values));
    }

    /**
     * @param array $keys
     * @param array $values
     *
     * @return self
     */
    public static function map(array $keys, array $values): self
    {
        return new self(Type\Collection::KIND_MAP, $keys, $values);
    }

    /**
     * @param array $values
     *
     * @return self
     */
    public static function assoc(array $values): self
    {
        return self::map(\array_keys($values), \array_values($values));
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return $this->keys;
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Buffer $buffer): void
    {
        $tmp = new Buffer;
        $tmp->appendInt(\count($this->keys));

        switch ($this->kind) {
            case Type\Collection::KIND_LIST:
            case Type\Collection::KIND_SET:
                foreach ($this->values as $value) {
                    $tmp->appendValue($value);
                }

                break;
            case Type\Collection::KIND_MAP:
                foreach ($this->keys as $i => $key) {
                    $tmp->appendValue($key);
                    $tmp->appendValue($this->values[$i]);
                }

                break;
        }

        $buffer
            ->appendInt($tmp->size())
            ->append($tmp->flush())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->values[\key($this->keys)];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        \next($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return \current($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->values[\key($this->keys)]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        \reset($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return \array_search($offset, $this->keys) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $key = \array_search($offset, $this->keys);

        return $this->values[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Collection is immutable.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Collection is immutable.');
    }
}
