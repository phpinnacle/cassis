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

use function Amp\asyncCall;
use function Amp\call;
use Amp\Promise;

final class EventEmitter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var callable[][]
     */
    private $listeners = [];

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string   $event
     * @param callable $listener
     *
     * @return Promise
     */
    public function register(string $event, callable $listener): Promise
    {
        return call(function() use ($event, $listener) {
            if (!isset($this->listeners[$event])) {
                yield $this->connection->send(new Request\Register([$event]));
            }

            $this->listeners[$event][] = $listener;
        });
    }

    /**
     * @param Frame $frame
     */
    public function emit(Frame $frame): void
    {
        asyncCall(function () use ($frame) {
            $event = null;

            switch (true) {
                case $frame instanceof Response\TopologyChange:
                    $event = new Event\TopologyChange($frame->change, \inet_ntop($frame->address));

                    break;
                case $frame instanceof Response\StatusChange:
                    $event = new Event\StatusChange($frame->change, \inet_ntop($frame->address));

                    break;
                case $frame instanceof Response\SchemaChange:
                    $event = new Event\SchemaChange(
                        $frame->change,
                        $frame->target,
                        $frame->keyspace,
                        $frame->name,
                        $frame->arguments
                    );

                    break;
            }

            if ($event === null) {
                return;
            }

            $class     = \get_class($event);
            $listeners = $this->listeners[$class] ?? [];

            foreach ($listeners as $listener) {
                asyncCall($listener, $event);
            }
        });
    }
}
