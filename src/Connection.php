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

use function Amp\asyncCall, Amp\call;
use Amp\Deferred;
use function Amp\Socket\connect;
use Amp\Loop;
use Amp\Promise;
use Amp\Socket\ClientConnectContext;
use Amp\Socket\Socket;

final class Connection
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var callable[]
     */
    private $callbacks = [];

    /**
     * @var int
     */
    private $lastWrite = 0;

    /**
     * @param string $uri
     */
    public function __construct(string $uri)
    {
        $this->uri    = $uri;
        $this->parser = new Parser;
    }

    /**
     * @param int      $stream
     * @param callable $callback
     */
    public function subscribe(int $stream, callable $callback): void
    {
        $this->callbacks[$stream][] = $callback;
    }

    /**
     * @param int $stream
     *
     * @return Promise
     */
    public function await(int $stream): Promise
    {
        $deferred = new Deferred;

        $this->subscribe($stream, function (Frame $frame) use ($deferred) {
            if ($frame instanceof Response\Error) {
                $deferred->fail(new Exception\ServerException($frame->message, $frame->code));
            } else {
                $deferred->resolve($frame);
            }

            return true;
        });

        return $deferred->promise();
    }

    /**
     * @param int $stream
     *
     * @return void
     */
    public function cancel(int $stream): void
    {
        unset($this->callbacks[$stream]);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param Frame $frame
     *
     * @return Promise
     */
    public function send(Frame $frame): Promise
    {
        $this->lastWrite = Loop::now();

        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->socket->write($frame->pack()->flush());
    }

    /**
     * @param int  $timeout
     * @param int  $attempts
     * @param bool $noDelay
     *
     * @return Promise
     */
    public function open(int $timeout, int $attempts, bool $noDelay): Promise
    {
        return call(function () use ($timeout, $attempts, $noDelay) {
            $context = (new ClientConnectContext)
                ->withConnectTimeout($timeout)
                ->withMaxAttempts($attempts)
            ;

            if ($noDelay) {
                $context->withTcpNoDelay();
            }

            $this->socket = yield connect($this->uri, $context);

            asyncCall(function () {
                while (null !== $chunk = yield $this->socket->read()) {
                    $this->parser->append($chunk);

                    while ($frame = $this->parser->parse()) {
                        if (!isset($this->callbacks[$frame->stream])) {
                            continue 2;
                        }

                        foreach ($this->callbacks[$frame->stream] as $i => $callback) {
                            if (yield call($callback, $frame)) {
                                unset($this->callbacks[$frame->stream][$i]);
                            }
                        }
                    }
                }

                unset($this->socket);
            });
        });
    }

    /**
     * @return void
     */
    public function close(): void
    {
        if ($this->socket !== null) {
            $this->socket->close();
        }

        $this->callbacks = [];
    }
}
