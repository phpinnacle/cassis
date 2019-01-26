<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Statement;

use PHPinnacle\Cassis\Statement;

final class Prepared implements Statement
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $values;

    /**
     * @param string $id
     * @param array  $values
     */
    public function __construct(string $id, array $values = [])
    {
        $this->id     = $id;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }
}
