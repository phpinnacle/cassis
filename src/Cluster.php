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
use Amp\Socket;
use Amp\Uri\Uri;

final class Cluster
{
    private const
        STATE_NOT_CONNECTED = 0,
        STATE_CONNECTING    = 1,
        STATE_CONNECTED     = 2
    ;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Streams
     */
    private $streams;

    /**
     * @var int
     */
    private $state = self::STATE_NOT_CONNECTED;

    /**
     * @var Events
     */
    private $events;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config  = $config;
        $this->streams = Streams::instance();
    }

    /**
     * @param string $dsn
     *
     * @return self
     */
    public static function build(string $dsn): self
    {
        return new self(Config::parse($dsn));
    }

    /**
     * @return Promise<array>
     */
    public function options(): Promise
    {
        return call(function () {
            /** @var Connection $connection */
            $connection = yield $this->open();

            /** @var Response\Supported $response */
            $response = yield $connection->send(new Request\Options);

            $connection->close();

            return $response->options;
        });
    }

    /**
     * @return Promise<Events>
     */
    public function events(): Promise
    {
        return call(function () {
            if ($this->events) {
                return $this->events;
            }

            return $this->events = new Events(yield $this->startup());
        });
    }

    /**
     * @param string $keyspace
     *
     * @return Promise<void>
     */
    public function connect(string $keyspace = null): Promise
    {
        return call(function () use ($keyspace) {
            if ($this->state !== self::STATE_NOT_CONNECTED) {
                throw Exception\ClientException::alreadyConnected();
            }

            $this->state = self::STATE_CONNECTING;

            $session = new Session(yield $this->startup());

            if ($keyspace !== null) {
                yield $session->keyspace($keyspace);
            }

            $this->state = self::STATE_CONNECTED;

            return $session;
        });
    }

    /**
     * @return Promise<Connection>
     */
    private function startup()
    {
        return call(function () {
            /** @var Connection $connection */
            $connection = yield $this->open();

            $request  = new Request\Startup($this->config->options());
            $response = yield $connection->send($request);

            if ($response instanceof Response\Authenticate) {
                yield $this->authenticate($connection);
            }

            return $connection;
        });
    }

    /**
     * @return Promise<Connection>
     */
    private function open(): Promise
    {
        return call(function () {
            $compressor = $this->detectCompressor();

            foreach ($this->config->hosts() as $host) {
                $connection = new Connection(new Uri($host), $this->streams, $compressor);

                try {
                    yield $connection->open(
                        $this->config->tcpTimeout(),
                        $this->config->tcpAttempts(),
                        $this->config->tcpNoDelay()
                    );

                    return $connection;
                } catch (Socket\ConnectException $error) {
                    continue;
                }
            }

            throw Exception\ClientException::couldNotConnect();
        });
    }

    /**
     * @param Connection $connection
     *
     * @return Promise<Response\AuthSuccess>
     */
    private function authenticate(Connection $connection): Promise
    {
        return call(function () use ($connection) {
            $request  = new Request\AuthResponse($this->config->user(), $this->config->password());
            $response = yield $connection->send($request);

            switch (true) {
                case $response instanceof Response\AuthSuccess:
                    return $response;
                default:
                    throw Exception\ServerException::unexpectedFrame($response->opcode);
            }
        });
    }

    /**
     * @return Compressor
     */
    private function detectCompressor(): Compressor
    {
        switch ($this->config->compression()) {
            case Config::COMPRESSION_NONE:
                return new Compressor\NoneCompressor;
            case Config::COMPRESSION_LZ4:
                return new Compressor\LzCompressor;
            case Config::COMPRESSION_SNAPPY:
                return new Compressor\SnappyCompressor;
            default:
                throw Exception\ConfigException::unknownCompressionMechanism($this->config->compression());
        }
    }
}
