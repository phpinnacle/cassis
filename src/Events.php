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

final class Events
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

        $this->connection->subscribe(-1, function (Frame $frame) {
            if ($frame->opcode !== Frame::OPCODE_EVENT) {
                return;
            }

            /** @var Response\Event $frame */
            $event     = $frame->event;
            $listeners = $this->listeners[$event->type()] ?? [];

            foreach ($listeners as $listener) {
                asyncCall($listener, $event);
            }
        });
    }

    /**
     * @param callable $listener
     *
     * @return Promise
     */
    public function onSchemaChange(callable $listener): Promise
    {
        return $this->register(Event::SCHEMA_CHANGE, $listener);
    }

    /**
     * @param callable $listener
     *
     * @return Promise
     */
    public function onStatusChange(callable $listener): Promise
    {
        return $this->register(Event::STATUS_CHANGE, $listener);
    }

    /**
     * @param callable $listener
     *
     * @return Promise
     */
    public function onTopologyChange(callable $listener): Promise
    {
        return $this->register(Event::TOPOLOGY_CHANGE, $listener);
    }

    /**
     * @param string   $event
     * @param callable $listener
     *
     * @return Promise
     */
    private function register(string $event, callable $listener): Promise
    {
        return call(function() use ($event, $listener) {
            if (!isset($this->listeners[$event])) {
                yield $this->connection->send(new Request\Register([$event]));
            }

            $this->listeners[$event][] = $listener;
        });
    }

    /**
     * Clear resources
     */
    public function __destruct()
    {
        $this->connection->close();
    }
}
