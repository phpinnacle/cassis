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

namespace PHPinnacle\Cassis\Event;

use PHPinnacle\Cassis\Event;

final class TopologyChange implements Event
{
    /**
     * @var string
     */
    private $change;

    /**
     * @var string
     */
    private $address;

    /**
     * @param string $change
     * @param string $address
     */
    public function __construct(string $change, string $address)
    {
        $this->change  = $change;
        $this->address = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return self::TOPOLOGY_CHANGE;
    }

    /**
     * @return string
     */
    public function change(): string
    {
        return $this->change;
    }

    /**
     * @return string
     */
    public function address(): string
    {
        return $this->address;
    }
}
