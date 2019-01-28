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

final class SchemaChange implements Result
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $options;

    /**
     * @param string $type
     * @param string $target
     * @param string $options
     */
    public function __construct(string $type, string $target, string $options)
    {
        $this->type    = $type;
        $this->target  = $target;
        $this->options = $options;
    }

    /**
     * @param Response\Result $frame
     *
     * @return self
     */
    public static function create(Response\Result $frame): self
    {
        $buffer = new Buffer($frame->data);

        return new self(
            $buffer->consumeString(),
            $buffer->consumeString(),
            $buffer->consumeString()
        );
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function target(): string
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function options(): string
    {
        return $this->options;
    }
}
