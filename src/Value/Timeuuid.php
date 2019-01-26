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

namespace PHPinnacle\Cassis\Value;

use Ramsey\Uuid\Uuid as Implementation;

final class Timeuuid extends Uuid
{
    /**
     * @return \DateTimeInterface
     */
    public function toDateTime(): \DateTimeInterface
    {
        return Implementation::fromString($this->value)->getDateTime();
    }
}
