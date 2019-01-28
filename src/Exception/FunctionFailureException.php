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

final class FunctionFailureException extends ServerException
{
    /**
     * @var string
     */
    private $keyspace;

    /**
     * @var string
     */
    private $function;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param string $message
     * @param string $keyspace
     * @param string $function
     * @param array  $arguments
     */
    public function __construct(string $message, string $keyspace, string $function, array $arguments)
    {
        parent::__construct($message);

        $this->keyspace  = $keyspace;
        $this->function  = $function;
        $this->arguments = $arguments;
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
    public function function(): string
    {
        return $this->function;
    }

    /**
     * @return array
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
