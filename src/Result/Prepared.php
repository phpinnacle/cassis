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

use PHPinnacle\Cassis\Metadata;
use PHPinnacle\Cassis\Response;
use PHPinnacle\Cassis\Result;

class Prepared implements Result
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Metadata
     */
    private $prepared;

    /**
     * @var Metadata
     */
    private $result;

    /**
     * @param string   $id
     * @param Metadata $prepared
     * @param Metadata $result
     */
    public function __construct(string $id, Metadata $prepared, Metadata $result)
    {
        $this->id       = $id;
        $this->prepared = $prepared;
        $this->result   = $result;
    }

    /**
     * @param Response\Result $frame
     *
     * @return self
     */
    public static function create(Response\Result $frame): self
    {
        return new self(
            $frame->data->consumeString(),
            Metadata::create($frame->data),
            Metadata::create($frame->data)
        );
    }
}
