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

namespace PHPinnacle\Cassis\Event;

use PHPinnacle\Cassis\Event;

final class SchemaChange implements Event
{
    /**
     * @var string
     */
    private $change;

    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $keyspace;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param string $change
     * @param string $target
     * @param string $keyspace
     * @param string $name
     * @param array  $arguments
     */
    public function __construct(
        string $change,
        string $target,
        string $keyspace,
        string $name = '',
        array $arguments = []
    ) {
        $this->change    = $change;
        $this->target    = $target;
        $this->keyspace  = $keyspace;
        $this->name      = $name;
        $this->arguments = $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return self::SCHEMA_CHANGE;
    }

    /**
     * @return string
     */
    public function change(): string
    {
        return $this->change;
    }

    /**
     * @return string
     */
    public function target(): string
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function keyspace(): string
    {
        return $this->keyspace;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
