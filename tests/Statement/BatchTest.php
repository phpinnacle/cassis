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

class BatchTest extends CassisTest
{
    public function testCreate()
    {
        $queries = [
            new Statement\Simple("SELECT * FROM table WHERE id = ?", [1]),
            new Statement\Simple("SELECT * FROM table WHERE id = ?", [2])
        ];

        $statement = Statement\Batch::logged(...$queries);

        self::assertEquals(0, $statement->type());
        self::assertEquals($queries, $statement->queries());

        $statement = Statement\Batch::unlogged(...$queries);

        self::assertEquals(1, $statement->type());
        self::assertEquals($queries, $statement->queries());

        $statement = Statement\Batch::counter(...$queries);

        self::assertEquals(2, $statement->type());
        self::assertEquals($queries, $statement->queries());
    }
}
