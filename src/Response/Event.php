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

namespace PHPinnacle\Cassis\Response;

use PHPinnacle\Cassis\Response;
use PHPinnacle\Cassis\Event as EventContract;

final class Event extends Response
{
    public $opcode = self::OPCODE_EVENT;

    /**
     * @var EventContract
     */
    public $event;

    /**
     * @param EventContract $event
     */
    public function __construct(EventContract $event)
    {
        $this->event = $event;
    }
}
