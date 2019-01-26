<?php

use Amp\Loop;
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Cluster;

require_once __DIR__ . '/../vendor/autoload.php';

Loop::run(function () use ($argv) {
    if (!$dsn = \getenv('CASSIS_BENCHMARK_DSN')) {
        echo 'No benchmark dsn! Please set CASSIS_BENCHMARK_DSN environment variable.', \PHP_EOL;

        Loop::stop();
    }

    $cluster = Cluster::build(\getenv('CASSIS_EXAMPLE_DSN'));
    /** @var Session $session */
    $session = yield $cluster->connect();
});
