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

final class Context
{
    const
        FLAG_VALUES             = 0x01,
        FLAG_SKIP_METADATA      = 0x02,
        FLAG_PAGE_SIZE          = 0x04,
        FLAG_PAGING_STATE       = 0x08,
        FLAG_SERIAL_CONSISTENCY = 0x10,
        FLAG_DEFAULT_TIMESTAMP  = 0x20,
        FLAG_NAMES_FOR_VALUES   = 0x40
    ;

    const
        CONSISTENCY_ANY          = 0x0000,
        CONSISTENCY_ONE          = 0x0001,
        CONSISTENCY_TWO          = 0x0002,
        CONSISTENCY_THREE        = 0x0003,
        CONSISTENCY_QUORUM       = 0x0004,
        CONSISTENCY_ALL          = 0x0005,
        CONSISTENCY_LOCAL_QUORUM = 0x0006,
        CONSISTENCY_EACH_QUORUM  = 0x0007,
        CONSISTENCY_SERIAL       = 0x0008,
        CONSISTENCY_LOCAL_SERIAL = 0x0009,
        CONSISTENCY_LOCAL_ONE    = 0x000A
    ;

    /**
     * @var int
     */
    private $consistency;

    /**
     * @var int
     */
    private $flags = 0;

    /**
     * @var array
     */
    private $values;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @var string
     */
    private $pagingState;

    /**
     * @var int
     */
    private $serialConsistency;

    /**
     * @var int
     */
    private $defaultTimestamp;

    /**
     * @param int $consistency
     */
    public function __construct(int $consistency = self::CONSISTENCY_ALL)
    {
        $this->consistency = $consistency;
    }

    /**
     * @return int
     */
    public function consistency(): int
    {
        return $this->consistency;
    }

    /**
     * @return int
     */
    public function flags(): int
    {
        return $this->flags;
    }

    /**
     * @param mixed[] $values
     *
     * @return self
     */
    public function withValues(array $values): self
    {
        $this->flags |= self::FLAG_VALUES;
        $this->values = $values;

        if (\array_keys($values) !== \range(0, \count($values) - 1)) {
            $this->flags |= self::FLAG_NAMES_FOR_VALUES;
        }

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyAny(): self
    {
        $this->consistency = self::CONSISTENCY_ANY;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyOne(): self
    {
        $this->consistency = self::CONSISTENCY_ONE;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyTwo(): self
    {
        $this->consistency = self::CONSISTENCY_TWO;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyThree(): self
    {
        $this->consistency = self::CONSISTENCY_THREE;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyQuorum(): self
    {
        $this->consistency = self::CONSISTENCY_QUORUM;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyAll(): self
    {
        $this->consistency = self::CONSISTENCY_ALL;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyLocalQuorum(): self
    {
        $this->consistency = self::CONSISTENCY_LOCAL_QUORUM;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyEachQuorum(): self
    {
        $this->consistency = self::CONSISTENCY_EACH_QUORUM;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencySerial(): self
    {
        $this->consistency = self::CONSISTENCY_SERIAL;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyLocalSerial(): self
    {
        $this->consistency = self::CONSISTENCY_LOCAL_SERIAL;

        return $this;
    }

    /**
     * @return self
     */
    public function consistencyLocalOne(): self
    {
        $this->consistency = self::CONSISTENCY_LOCAL_ONE;

        return $this;
    }

    /**
     * @return self
     */
    public function skipMetadata(): self
    {
        $this->flags |= self::FLAG_SKIP_METADATA;

        return $this;
    }

    /**
     * @param int $size
     *
     * @return self
     */
    public function pageSize(int $size): self
    {
        $this->flags |= self::FLAG_PAGE_SIZE;
        $this->pageSize = $size;

        return $this;
    }

    /**
     * @param string $state
     *
     * @return self
     */
    public function pagingState(string $state): self
    {
        $this->flags |= self::FLAG_PAGING_STATE;
        $this->pagingState = $state;

        return $this;
    }

    /**
     * @param int $consistency
     *
     * @return self
     */
    public function serialConsistency(int $consistency): self
    {
        $this->flags |= self::FLAG_SERIAL_CONSISTENCY;
        $this->serialConsistency = $consistency;

        return $this;
    }

    /**
     * @param int $timestamp
     *
     * @return self
     */
    public function defaultTimestamp(int $timestamp): self
    {
        $this->flags |= self::FLAG_DEFAULT_TIMESTAMP;
        $this->defaultTimestamp = $timestamp;

        return $this;
    }

    /**
     * @param Buffer $buffer
     *
     * @return Buffer
     */
    public function queryParameters(Buffer $buffer): string
    {
        $flags = $this->flags;

        $buffer->appendShort($this->consistency);
        $buffer->appendByte($flags);

        if ($this->flags & self::FLAG_VALUES) {
            if ($this->flags & self::FLAG_NAMES_FOR_VALUES) {
                $buffer->appendValuesMap($this->values);
            } else {
                $buffer->appendValuesList($this->values);
            }
        }

        if ($this->flags & self::FLAG_PAGE_SIZE) {
            $buffer->appendInt($this->pageSize);
        }

        if ($this->flags & self::FLAG_PAGING_STATE) {
            $buffer->appendBytes($this->pagingState);
        }

        if ($this->flags & self::FLAG_SERIAL_CONSISTENCY) {
            $buffer->appendShort($this->serialConsistency);
        }

        if ($this->flags & self::FLAG_DEFAULT_TIMESTAMP) {
            $buffer->appendLong($this->defaultTimestamp);
        }

        return $buffer->flush();
    }
}
