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

namespace PHPinnacle\Cassis\Result;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Response;
use PHPinnacle\Cassis\Result;

final class SetKeyspace implements Result
{
    /**
     * @var string
     */
    public $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param Response\Result $frame
     *
     * @return self
     */
    public static function create(Response\Result $frame): self
    {
        return new self((new Buffer($frame->data))->consumeString());
    }
}
