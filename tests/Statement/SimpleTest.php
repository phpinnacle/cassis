<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Tests\Statement;

use PHPinnacle\Cassis\Tests\CassisTest;
use PHPinnacle\Cassis\Statement;

class SimpleTest extends CassisTest
{
    public function testCreate()
    {
        $cql = "SELECT * FROM table WHERE id = ?";
        $arguments = [1];

        $statement = new Statement\Simple($cql, $arguments);

        self::assertEquals($cql, $statement->cql());
        self::assertEquals($arguments, $statement->values());
    }
}
