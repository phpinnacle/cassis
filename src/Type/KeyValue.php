<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Type;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Type;

final class KeyValue implements Type
{
    /**
     * @var Type
     */
    private $key;

    /**
     * @var Type
     */
    private $value;

    /**
     * @param Type $key
     * @param Type $value
     */
    public function __construct(Type $key, Type $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @param Buffer $buffer
     *
     * @return mixed
     */
    public function read(Buffer $buffer)
    {
        return [
            $this->key->read($buffer),
            $this->value->read($buffer)
        ];
    }
}
