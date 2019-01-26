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

final class Simple implements Statement
{
    /**
     * @var string
     */
    private $cql;

    /**
     * @var array
     */
    private $values;

    /**
     * @param string $cql
     * @param array  $values
     */
    public function __construct(string $cql, array $values = [])
    {
        $this->cql    = $cql;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function cql(): string
    {
        return $this->cql;
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }
}
