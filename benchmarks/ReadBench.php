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

use PHPinnacle\Cassis\Context;

/**
 * @BeforeMethods({"setUp", "seedData"})
 */
class ReadBench extends CassisBench
{
    /**
     * @return void
     * @throws \Throwable
     */
    public function seedData(): void
    {
        $writer = new WriteBench;
        $writer->setUp();

        for ($i = 0; $i < 10; $i++) {
            $writer->benchWrite();
        }
    }

    /**
     * @WarmUp(1)
     * @Revs(10)
     * @Iterations(5)
     *
     * @return void
     * @throws \Throwable
     */
    public function benchRead()
    {
        $this->wait(function () {
            $toRead   = 100;
            $context  = (new Context)->limit($toRead);
            $total    = 0;

            /** @var \PHPinnacle\Cassis\Result\Rows $result */
            while ($result = yield $this->session->query("SELECT * FROM posts_by_user;", [], $context)) {
                $total = $total + \count($result);

                if (!$cursor = $result->cursor()) {
                    break;
                }

                $context->offset($cursor);
            };
        });
    }
}
