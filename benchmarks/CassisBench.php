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

use Amp\Loop;
use function Amp\call, Amp\Promise\wait;
use PHPinnacle\Cassis\Cluster;
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Value;

/**
 * @BeforeMethods({"setUp"})
 * @AfterMethods({"tearDown"})
 */
abstract class CassisBench
{
    private const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @var Cluster
     */
    protected $cluster;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @return void
     * @throws \Throwable
     */
    public function setUp(): void
    {
        $this->wait(function () {
            if (!$dsn = \getenv('CASSIS_BENCHMARK_DSN')) {
                throw new \RuntimeException('No benchmark dsn! Please set CASSIS_BENCHMARK_DSN environment variable.');
            }

            $this->cluster = Cluster::build($dsn);
            $this->session = yield $this->cluster->connect();

            $setup = [
                "CREATE KEYSPACE IF NOT EXISTS blogs WITH replication = {'class': 'SimpleStrategy', 'replication_factor': 1 };",
                "USE blogs;",
                "CREATE TYPE IF NOT EXISTS user (id int, name text, enabled boolean);",
                "CREATE TABLE IF NOT EXISTS posts_by_user (
                    author frozen<user>,
                    post_id timeuuid,
                    text text,
                    date timestamp,
                    tags set<text>,
                    PRIMARY KEY ((author), post_id)
                ) WITH CLUSTERING ORDER BY (post_id DESC);",
            ];

            foreach ($setup as $query) {
                yield $this->session->query($query);
            }
        });
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function tearDown(): void
    {
        $this->wait(function () {
            yield $this->session->query("DROP KEYSPACE IF EXISTS blogs;");

            $this->session->close();
        });
    }

    /**
     * @param callable $action
     * @throws \Throwable
     */
    public function wait(callable $action): void
    {
        wait(call($action));
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function randomString(int $length): string
    {
        $count  = \strlen(self::ALPHABET);
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= self::ALPHABET[\rand(0, $count - 1)];
        }

        return $string;
    }

    /**
     * @return Value\Timestamp
     */
    public function randomDate(): Value\Timestamp
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return Value\Timestamp::fromDateTime(new \DateTimeImmutable);
    }

    /**
     * @param int $count
     * @param int $length
     *
     * @return Value\Collection
     */
    public function randomTags(int $count, int $length): Value\Collection
    {
        $tags = [];

        for ($i = 0; $i < $count; $i++) {
            $tags[] = $this->randomString($length);
        }

        return Value\Collection::set($tags);
    }
}
