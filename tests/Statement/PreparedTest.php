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

class PreparedTest extends CassisTest
{
    public function testCreate()
    {
        $id = "2b34b8b0-39b8-11e9-b210-d663bd873d93";
        $arguments = [1];

        $statement = new Statement\Prepared($id, $arguments);

        self::assertEquals($id, $statement->id());
        self::assertEquals($arguments, $statement->values());
    }
}
