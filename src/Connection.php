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
use function Amp\Socket\connect, Amp\Socket\cryptoConnect;
use Amp\Deferred;
use Amp\Loop;
use Amp\Promise;
use Amp\Socket\ClientConnectContext;
use Amp\Socket\ClientTlsContext;
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
     * @var callable[]
     */
    private $callbacks = [];

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
        $this->queue   = new \SplQueue;
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

        $this->subscribe($stream, function (Frame $frame) use ($deferred) {
            if ($frame->opcode === Frame::OPCODE_ERROR) {
                /** @var Response\Error $frame */
                $deferred->fail($frame->exception);
            } else {
                $deferred->resolve($frame);
            }

            $this->streams->release($frame->stream);
        });

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
     * @param int      $stream
     * @param callable $handler
     *
     * @return void
     */
    public function subscribe(int $stream, callable $handler): void
    {
        $this->callbacks[$stream] = $handler;
    }

    /**
     * @param int  $timeout
     * @param int  $attempts
     * @param bool $noDelay
     *
     * @return Promise<self>
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

            return $this;
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
    
    /**
     * @return void
     */
    private function write(): void
    {
        asyncCall(function () {
            $done = 0;
            $data = '';

            while ($this->queue->isEmpty() === false) {
                $data .= $this->queue->dequeue();

                ++$done;

                if ($done % self::WRITE_ROUNDS === 0) {
                    Loop::defer(function () {
                        $this->write();
                    });

                    break;
                }
            }

            yield $this->socket->write($data);

            $this->lastWrite  = Loop::now();
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
                    if (!isset($this->callbacks[$frame->stream])) {
                        continue 2;
                    }

                    asyncCall($this->callbacks[$frame->stream], $frame);

                    unset($this->callbacks[$frame->stream]);
                }
            }

            $this->socket = null;
        });
    }
}
