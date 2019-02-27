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
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $keyspace;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $keyspace
     *
     * @return Promise<string>
     */
    public function keyspace(string $keyspace): Promise
    {
        return call(function () use ($keyspace) {
            if ($this->keyspace === $keyspace) {
                return $this->keyspace;
            }

            yield $this->execute(new Statement\Simple("USE {$keyspace}"));

            return $this->keyspace = $keyspace;
        });
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
     * @param string $cql
     *
     * @return Promise<Result\Prepared>
     */
    public function prepare(string $cql): Promise
    {
        return call(function () use ($cql) {
            $request  = new Request\Prepare($cql);
            $response = yield $this->connection->send($request);

            if ($response->kind !== Result::PREPARE) {
                throw new Exception\ServerException;
            }

            return Result\Prepared::create($response);
        });
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
            /** @var Response\Result $response */
            $request  = $this->request($statement, $context ?: new Context);
            $response = yield $this->connection->send($request);

            switch ($response->kind) {
                case Result::VOID:
                    return null;
                case Result::ROWS:
                    return Result\Rows::create($response);
                case Result::USE:
                    return Result\SetKeyspace::create($response);
                case Result::ALTER:
                    return Result\SchemaChange::create($response);
                default:
                    throw new Exception\ServerException;
            }
        });
    }

    /**
     * @param Statement $statement
     * @param Context   $context
     *
     * @return Request
     */
    private function request(Statement $statement, Context $context): Request
    {
        switch (true) {
            case $statement instanceof Statement\Simple:
                return new Request\Query($statement->cql(), $context->arguments($statement->values()));
            case $statement instanceof Statement\Prepared:
                return new Request\Execute($statement->id(), $context->arguments($statement->values()));
            case $statement instanceof Statement\Batch:
                return new Request\Batch($statement->type(), $statement->queries(), $context);
            default:
                throw new Exception\ClientException;
        }
    }

    /**
     * @return void
     */
    public function close(): void
    {
        $this->connection->close();
    }
}
