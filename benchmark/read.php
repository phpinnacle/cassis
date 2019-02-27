<?php

use Amp\Loop;
use PHPinnacle\Cassis\Context;
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Cluster;
use PHPinnacle\Cassis\Value;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../vendor/autoload.php';

Loop::run(function () use ($argv) {
    if (!$dsn = \getenv('CASSIS_BENCHMARK_DSN')) {
        echo 'No benchmark dsn! Please set CASSIS_BENCHMARK_DSN environment variable.', \PHP_EOL;

        Loop::stop();
    }

    $cluster = Cluster::build(\getenv('CASSIS_BENCHMARK_DSN'));
    /** @var Session $session */
    $session = yield $cluster->connect();
    $setup   = require __DIR__ . '/shared.php';

    $watcher = Loop::onSignal(SIGTERM, function () use ($session) {
        $session->close();
    });

    try {
        foreach ($setup as $query) {
            yield $session->query($query);
        }

        $toWrite = $argv[1] ?? 10000;
        $toRead  = $argv[2] ?? 1000;

        $promises = [];

        for ($i = 1; $i <= $toWrite; $i++) {
            $author = new Value\UserDefined([
                'id'      => $i,
                'name'    => "User $i",
                'enabled' => (bool) ($i % 2),
            ]);

            $arguments = [
                'author'  => $author,
                'post_id' => Uuid::uuid1(),
                'text'    => random_string(500),
                'date'    => Value\Timestamp::fromDateTime(random_date()),
                'tags'    => Value\Collection::set(random_tags(\rand(1, 10), 5)),
            ];

            $fields = \implode(',', \array_keys($arguments));
            $values = \implode(',', \array_fill(0, \count($arguments), '?'));

            $promises[] = $session->query("INSERT INTO posts_by_user ($fields) VALUES ($values)", $arguments);
        }

        yield $promises;

        echo \sprintf("Done %d inserts. Start reading...\n", $toWrite);

        $time     = \microtime(true);
        $context  = (new Context)->limit($toRead);
        $total    = 0;

        /** @var \PHPinnacle\Cassis\Result\Rows $result */
        while ($result = yield $session->query("SELECT * FROM posts_by_user;", [], $context)) {
            $count = \count($result);
            $total = $total + $count;

            echo \sprintf("Read %d rows.\n", $count);

            if (!$cursor = $result->cursor()) {
                break;
            }

            $context->offset($cursor);
        };

        echo \sprintf("Read %d rows in %f seconds.\n", $total, \microtime(true) - $time);
    } catch (\Throwable $error) {
        echo "Got error: {$error->getMessage()}.\n";
    } finally {
        yield $session->query("DROP KEYSPACE IF EXISTS blogs;");
    }

    $session->close();

    Loop::cancel($watcher);
});
