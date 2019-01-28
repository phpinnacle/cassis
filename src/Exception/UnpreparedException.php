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

final class UnpreparedException extends ServerException
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $message
     * @param string $id
     */
    public function __construct(string $message, string $id)
    {
        parent::__construct($message);

        $this->id = $id;
    }
}
