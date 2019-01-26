<?php

use Amp\Loop;
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Cluster;

require __DIR__ . '/../vendor/autoload.php';

Loop::run(function () {
    if (!$dsn = \getenv('CASSIS_EXAMPLE_DSN')) {
        echo 'No example dsn! Please set CASSIS_EXAMPLE_DSN environment variable.', \PHP_EOL;

        Loop::stop();
    }

    $cluster = Cluster::build(\getenv('CASSIS_EXAMPLE_DSN'));
    /** @var Session $session */
    $session = yield $cluster->connect('system');
    $result  = yield $session->query('SELECT keyspace_name, columnfamily_name FROM schema_columnfamilies');

    foreach ($result as $row) {
        printf("The keyspace %s has a table called %s\n", $row['keyspace_name'], $row['columnfamily_name']);
    }
});
