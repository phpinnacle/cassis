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
use Amp\Socket\ClientTlsContext;
use function Amp\Socket\connect, Amp\Socket\cryptoConnect;
use Amp\Deferred;
use Amp\Loop;
use Amp\Promise;
use Amp\Socket\ClientConnectContext;
use Amp\Socket\Socket;
use Amp\Uri\Uri;

final class Connection
{
    const WRITE_ROUNDS = 64;

    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var Streams
     */
    private $streams;
    
    /**
     * @var Buffer
     */
    private $packer;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var EventEmitter
     */
    private $emitter;

    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var bool
     */
    private $processing = false;

    /**
     * @var Deferred[]
     */
    private $defers = [];

    /**
     * @var int
     */
    private $lastWrite = 0;

    /**
     * @param Uri        $uri
     * @param Streams    $streams
     * @param Compressor $compressor
     */
    public function __construct(Uri $uri, Streams $streams, Compressor $compressor)
    {
        $this->uri     = $uri;
        $this->streams = $streams;
        $this->packer  = new Packer($compressor);
        $this->parser  = new Parser($compressor);
        $this->emitter = new EventEmitter($this);
        $this->queue   = new \SplQueue;
    }

    /**
     * @param string   $event
     * @param callable $listener
     *
     * @return Promise
     */
    public function register(string $event, callable $listener): Promise
    {
        return $this->emitter->register($event, $listener);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param Request $request
     *
     * @return Promise
     */
    public function send(Request $request): Promise
    {
        $stream   = $this->streams->reserve();
        $deferred = new Deferred;

        $this->defers[$stream] = $deferred;

        $this->queue->enqueue($this->packer->pack($request, $stream));

        if ($this->processing === false) {
            $this->processing = true;

            Loop::defer(function () {
                $this->write();
            });
        }

        return $deferred->promise();
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
            $clientContext = new ClientConnectContext;

            if ($attempts > 0) {
                $clientContext = $clientContext->withMaxAttempts($attempts);
            }

            if ($timeout > 0) {
                $clientContext = $clientContext->withConnectTimeout($timeout);
            }

            if ($noDelay) {
                $clientContext = $clientContext->withTcpNoDelay();
            }

            $uri = \sprintf('tcp://%s:%d', $this->uri->getHost(), $this->uri->getPort());

            if ($this->uri->getScheme() === 'tls') {
                $cryptoContext = new ClientTlsContext;

                $this->socket = yield cryptoConnect($uri, $clientContext, $cryptoContext);
            } else {
                $this->socket = yield connect($uri, $clientContext);
            }

            $this->listen();
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

        $this->defers = [];
    }
    
    /**
     * @return void
     */
    private function write(): void
    {
        asyncCall(function () {
            $processed = 0;
            $data = '';

            while ($this->queue->isEmpty() === false) {
                $data .= $this->queue->dequeue();

                ++$processed;

                if ($processed % self::WRITE_ROUNDS === 0) {
                    Loop::defer(function () {
                        $this->write();
                    });

                    break;
                }
            }

            yield $this->socket->write($data);

            $this->lastWrite = Loop::now();

            $this->processing = false;
        });
    }

    /**
     * @return void
     */
    private function listen(): void
    {
        asyncCall(function () {
            while (null !== $chunk = yield $this->socket->read()) {
                $this->parser->append($chunk);

                while ($frame = $this->parser->parse()) {
                    if ($frame->opcode === Frame::OPCODE_EVENT) {
                        /** @var Response\Event $frame */
                        $this->emitter->emit($frame);

                        continue 2;
                    }

                    if (!isset($this->defers[$frame->stream])) {
                        continue 2;
                    }

                    $deferred = $this->defers[$frame->stream];
                    unset($this->defers[$frame->stream]);

                    $this->streams->release($frame->stream);

                    if ($frame->opcode === Frame::OPCODE_ERROR) {
                        /** @var Response\Error $frame */
                        $deferred->fail($frame->exception);
                    } else {
                        $deferred->resolve($frame);
                    }
                }
            }

            $this->socket = null;
        });
    }
}
