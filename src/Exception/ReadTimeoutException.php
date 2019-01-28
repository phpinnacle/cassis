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

namespace PHPinnacle\Cassis\Exception;

final class ReadTimeoutException extends TimeoutException
{
    /**
     * @var int
     */
    private $consistency;

    /**
     * @var int
     */
    private $received;

    /**
     * @var int
     */
    private $blockfor;

    /**
     * @var bool
     */
    private $dataPresent;

    /**
     * @param string $message
     * @param int    $consistency
     * @param int    $received
     * @param int    $blockfor
     * @param bool   $dataPresent
     */
    public function __construct(string $message, int $consistency, int $received, int $blockfor, bool $dataPresent)
    {
        parent::__construct($message);

        $this->consistency = $consistency;
        $this->received    = $received;
        $this->blockfor    = $blockfor;
        $this->dataPresent = $dataPresent;
    }

    /**
     * @return int
     */
    public function consistency(): int
    {
        return $this->consistency;
    }

    /**
     * @return int
     */
    public function received(): int
    {
        return $this->received;
    }

    /**
     * @return int
     */
    public function blockfor(): int
    {
        return $this->blockfor;
    }

    /**
     * @return bool
     */
    public function dataPresent(): bool
    {
        return $this->dataPresent;
    }
}
