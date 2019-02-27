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
        STATE_CONNECTED     = 2,
        STATE_DISCONNECTING = 3
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
     * @var Connection
     */
    private $connection;

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
            if ($this->connection === null) {
                $this->connection = yield $this->open();
            }

            /** @var Response\Supported $response */
            $response = yield $this->connection->send(new Request\Options);

            return $response->options;
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

            if (null === $this->connection) {
                $this->connection = yield $this->open();
            }

            $frame = yield $this->connection->send(new Request\Startup($this->config->options()));

            if ($frame instanceof Response\Authenticate) {
                yield $this->authenticate();
            }

            $session = new Session($this->connection);

            if ($keyspace !== null) {
                yield $session->keyspace($keyspace);
            }

            $this->state = self::STATE_CONNECTED;

            return $session;
        });
    }

    /**
     * @param int    $code
     * @param string $reason
     *
     * @return Promise<void>
     */
    public function disconnect(int $code = 0, string $reason = ''): Promise
    {
        return call(function() use ($code, $reason) {
            if ($this->state === self::STATE_DISCONNECTING) {
                return;
            }

            $this->state = self::STATE_DISCONNECTING;

            if ($this->connection !== null) {
                $this->connection->close();
            }

            $this->state = self::STATE_NOT_CONNECTED;
        });
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->state === self::STATE_CONNECTED;
    }

    /**
     * @return Promise
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
     * @return Promise
     */
    private function authenticate(): Promise
    {
        return call(function () {
            $request = new Request\AuthResponse(
                $this->config->user(),
                $this->config->password()
            );

            /** @var Frame $frame */
            $frame = yield $this->connection->send($request);

            switch (true) {
                case $frame instanceof Response\AuthChallenge:
                    // TODO

                    break;
                case $frame instanceof Response\AuthSuccess:
                    break;
                default:
                    throw Exception\ServerException::unexpectedFrame($frame->opcode);
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
