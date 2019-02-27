# PHPinnacle Cassis

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This library is a pure asynchronous PHP implementation of the Cassandra V4 binary protocol.
It utilize [amphp](https://amphp.org) framework for async operations.

## UNDER DEVELOPMENT
## NOT READY FOR PRODUCTION

## Install

Via Composer

```bash
$ composer require phpinnacle/cassis
```

## Basic Usage

```php
<?php

use Amp\Loop;
use PHPinnacle\Cassis\Cluster;
use PHPinnacle\Cassis\Session;
use PHPinnacle\Cassis\Statement;

require __DIR__ . '/vendor/autoload.php';

Loop::run(function () {
    $cluster = Cluster::build('tcp://localhost:9042');
    
    /** @var Session $session */
    $session = yield $cluster->connect('system');

    $statement = new Statement\Simple('SELECT keyspace_name, columnfamily_name FROM schema_columnfamilies');
    $result    = yield $session->execute($statement);

    foreach ($result as $row) {
        printf("The keyspace %s has a table called %s\n", $row['keyspace_name'], $row['columnfamily_name']);
    }

    yield $cluster->disconnect();
});

```

More examples can be found in [`examples`](examples) directory. Run it with:
```bash
CASSIS_EXAMPLE_DSN=tcp://user:pass@localhost:9042 php example/*
```

## Benchmark

Benchmarks were run as:

```bash
CASSIS_BENCHMARK_DSN=tcp://user:pass@localhost:9042 php benchmark/write.php N
CASSIS_BENCHMARK_DSN=tcp://user:pass@localhost:9042 php benchmark/read.php N M
```

## Testing

```bash
$ CASSIS_TEST_DSN=tcp://user:pass@localhost:9042 composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email dev@phpinnacle.com instead of using the issue tracker.

## Credits

- [PHPinnacle][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/phpinnacle/cassis.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/phpinnacle/cassis.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/phpinnacle/cassis.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/phpinnacle/cassis.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/phpinnacle/cassis
[link-scrutinizer]: https://scrutinizer-ci.com/g/phpinnacle/cassis/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/phpinnacle/cassis
[link-downloads]: https://packagist.org/packages/phpinnacle/cassis
[link-author]: https://github.com/phpinnacle
[link-contributors]: https://github.com/orgs/phpinnacle/people
