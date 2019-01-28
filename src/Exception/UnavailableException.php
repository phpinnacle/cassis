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

final class UnavailableException extends ServerException
{
    /**
     * @var int
     */
    private $consistency;

    /**
     * @var int
     */
    private $required;

    /**
     * @var int
     */
    private $alive;

    /**
     * @param string $message
     * @param int    $consistency
     * @param int    $required
     * @param int    $alive
     */
    public function __construct(string $message, int $consistency, int $required, int $alive)
    {
        parent::__construct($message);

        $this->consistency = $consistency;
        $this->required    = $required;
        $this->alive        = $alive;
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
    public function required(): int
    {
        return $this->required;
    }

    /**
     * @return int
     */
    public function alive(): int
    {
        return $this->alive;
    }
}
