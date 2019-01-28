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

namespace PHPinnacle\Cassis\Exception;

final class AlreadyExistsException extends ServerException
{
    /**
     * @var string
     */
    private $keyspace;

    /**
     * @var string
     */
    private $table;

    /**
     * @param string $message
     * @param string $keyspace
     * @param string $table
     */
    public function __construct(string $message, string $keyspace, string $table)
    {
        parent::__construct($message);

        $this->keyspace = $keyspace;
        $this->table    = $table;
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
    public function table(): string
    {
        return $this->table;
    }
}
