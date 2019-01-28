<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Tests\Value;

use PHPinnacle\Cassis\Buffer;
use PHPinnacle\Cassis\Tests\CassisTest;
use PHPinnacle\Cassis\Type;
use PHPinnacle\Cassis\Type\Base;
use PHPinnacle\Cassis\Value\UserDefined;

class UserDefinedTest extends CassisTest
{
    public function testValues()
    {
        $args = [
            'id'   => 1,
            'name' => 'John Doe',
        ];

        $user = new UserDefined($args);

        self::assertEquals($args, $user->values());
    }

    public function testWrite()
    {
        $buffer = new Buffer;
        $value  = new UserDefined([
            'id'   => 1,
            'name' => 'John Doe',
        ]);

        $buffer->appendValue($value);

        $type = new Type\UserDefined('test', 'user', [
            'id'   => new Base(Base::INT),
            'name' => new Base(Base::TEXT),
        ]);

        self::assertEquals($value, $type->read($buffer));
    }
}
