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

namespace PHPinnacle\Cassis;

use function Amp\call;
use Amp\Promise;

final class Session
{
    private const
        RESPONSE_VOID    = 0x0001,
        RESPONSE_ROWS    = 0x0002,
        RESPONSE_USE     = 0x0003,
        RESPONSE_PREPARE = 0x0004,
        RESPONSE_ALTER   = 0x0005
    ;

    private const MAX_STREAM = 32768;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var int
     */
    private $stream = 0;
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string  $cql
     * @param array   $values
     * @param Context $context
     *
     * @return Promise<Result>
     */
    public function query(string $cql, array $values = [], Context $context = null): Promise
    {
        return $this->execute(new Statement\Simple($cql, $values), $context);
    }

    /**
     * @param Statement $statement
     * @param Context   $context
     *
     * @return Promise<Result>
     */
    public function execute(Statement $statement, Context $context = null): Promise
    {
        return call(function () use ($statement, $context) {
            $stream  = $this->reserveStream();
            $context = $context ?: new Context;

            switch (true) {
                case $statement instanceof Statement\Simple:
                    $query = new Request\Query($stream, $statement->cql(), $statement->values(), $context);

                    break;
                case $statement instanceof Statement\Prepared:
                    $query = new Request\Execute($stream, $statement->id(), $statement->values(), $context);

                    break;
                case $statement instanceof Statement\Batch:
                    $query = new Request\Batch($stream, $statement->type(), $statement->queries(), $context);
                    break;
                default:
                    throw new Exception\ClientException;
            }

            yield $this->connection->send($query);

            /** @var Response\Result $frame */
            $frame = yield $this->connection->await($stream);

            $this->releaseStream($stream);

            switch ($frame->kind) {
                case self::RESPONSE_VOID:
                    return null;
                case self::RESPONSE_ROWS:
                    return Result\Rows::create($frame);
                case self::RESPONSE_USE:
                    return Result\SetKeyspace::create($frame);
                case self::RESPONSE_ALTER:
                    return Result\SchemaChange::create($frame);
                default:
                    throw new Exception\ServerException;
            }
        });
    }

    /**
     * @param string $cql
     *
     * @return Promise<Prepared>
     */
    public function prepare(string $cql): Promise
    {
        return call(function () use ($cql) {
            $stream  = $this->reserveStream();
            $prepare = new Request\Prepare($stream, $cql);

            yield $this->connection->send($prepare);

            /** @var Response\Result $frame */
            $frame = yield $this->connection->await($stream);

            $this->releaseStream($stream);

            if ($frame->kind !== self::RESPONSE_PREPARE) {
                throw new Exception\ServerException;
            }

            return Result\Prepared::create($frame);
        });
    }

    /**
     * @return Promise
     */
    public function close(): Promise
    {
        return call(function () {

        });
    }

    /**
     * @return int
     */
    private function reserveStream(): int
    {
        return $this->stream++;
    }

    /**
     * @param int $stream
     */
    private function releaseStream(int $stream): void
    {
        // TODO
    }
}
