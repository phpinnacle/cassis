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
     * @var int
     */
    private $state = self::STATE_NOT_CONNECTED;

    /**
     * @var Session[]
     */
    private $sessions = [];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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
     * @param string $keyspace
     *
     * @return Promise<void>
     */
    public function connect(string $keyspace = null): Promise
    {
        return call(function () use ($keyspace) {
            if ($this->state !== self::STATE_NOT_CONNECTED) {
                throw new \RuntimeException('Client already connected/connecting');
            }

            $this->state = self::STATE_CONNECTING;

            $this->connection = new Connection($this->config->uri());

            yield $this->connection->open(
                $this->config->tcpTimeout(),
                $this->config->tcpAttempts(),
                $this->config->tcpNoDelay()
            );

            yield $this->connection->send(new Request\Startup($this->config->options()));

            $frame = yield $this->connection->await(0);

            if ($frame instanceof Response\Authenticate) {
                yield $this->authenticate();
            }

            $session = new Session($this->connection);

            if ($keyspace !== null) {
                yield $session->execute(new Statement\Simple("USE {$keyspace}"));
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
            if (\in_array($this->state, [self::STATE_NOT_CONNECTED, self::STATE_DISCONNECTING])) {
                return;
            }

            if ($this->state !== self::STATE_CONNECTED) {
                throw new Exception\ClientException('Client is not connected');
            }

            $this->state = self::STATE_DISCONNECTING;

            if ($code === 0) {
                $promises = [];

                foreach($this->sessions as $id => $session) {
                    $promises[] = $session->close($code, $reason);
                }

                yield $promises;
            }

            $this->connection->close();

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
    private function authenticate(): Promise
    {
        return call(function () {
            yield $this->connection->send(new Request\AuthResponse(
                $this->config->user(),
                $this->config->password()
            ));

            yield $this->connection->await(0);
        });
    }
}
