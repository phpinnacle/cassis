<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPinnacle\Cassis\Tests;

use Amp\Loop;
use function Amp\call;
use PHPinnacle\Cassis\Cluster;

abstract class AsyncTest extends CassisTest
{
    /**
     * @var string
     */
    private $realTestName;

    /**
     * @codeCoverageIgnore Invoked before code coverage data is being collected.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        parent::setName($name);

        $this->realTestName = $name;
    }

    protected function runTest()
    {
        parent::setName('runTestAsync');

        return parent::runTest();
    }

    protected function runTestAsync(...$args)
    {
        $return = null;

        try {
            Loop::run(function () use (&$return, $args) {
                $return = yield call([$this, $this->realTestName], ...$args);

                $info  = Loop::getInfo();
                $count = $info['enabled_watchers']['referenced'];

                if ($count !== 0) {
                    $message = "Still have {$count} loop watchers.";

                    foreach (['defer', 'delay', 'repeat', 'on_readable', 'on_writable'] as $key) {
                        $message .= " {$key} - {$info[$key]['enabled']}.";
                    }

                    self::markTestIncomplete($message);

                    Loop::stop();
                }
            });
        } finally {
            Loop::set((new Loop\DriverFactory)->create());

            \gc_collect_cycles();
        }

        return $return;
    }

    /**
     * @return Cluster
     */
    public static function cluster(): Cluster
    {
        if (!$dsn = \getenv('CASSIS_TEST_DSN')) {
            self::markTestSkipped('No test dsn! Please set CASSIS_TEST_DSN environment variable.');
        }

        return Cluster::build($dsn);
    }
}
