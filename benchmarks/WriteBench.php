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

namespace PHPinnacle\Cassis\Benchmarks;

use PHPinnacle\Cassis\Value\UserDefined;
use Ramsey\Uuid\Uuid;

class WriteBench extends CassisBench
{
    /**
     * @WarmUp(1)
     * @Revs(10)
     * @Iterations(5)
     *
     * @return void
     * @throws \Throwable
     */
    public function benchWrite(): void
    {
        $this->wait(function () {
            $count    = 100;
            $promises = [];

            for ($i = 1; $i <= $count; $i++) {
                $author = new UserDefined([
                    'id'      => $i,
                    'name'    => "User $i",
                    'enabled' => (bool) ($i % 2),
                ]);

                $arguments = [
                    'author'  => $author,
                    'post_id' => Uuid::uuid1(),
                    'text'    => $this->randomString(500),
                    'date'    => $this->randomDate(),
                    'tags'    => $this->randomTags(\rand(1, 10), 5),
                ];

                $fields = \implode(',', \array_keys($arguments));
                $values = \implode(',', \array_fill(0, \count($arguments), '?'));

                $promises[] = $this->session->query("INSERT INTO posts_by_user ($fields) VALUES ($values)", $arguments);
            }

            yield $promises;
        });
    }
}
