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

use PHPinnacle\Cassis\Config;
use PHPinnacle\Cassis\Exception\ConfigException;

class ConfigTest extends CassisTest
{
    public function testCreate()
    {
        $config = new Config(['tcp://localhost:9042'], 'user', 'pass');

        self::assertEquals(['tcp://localhost:9042'], $config->hosts());
        self::assertEquals('user', $config->user());
        self::assertEquals('pass', $config->password());
    }

    public function testNoUser()
    {
        $config = new Config(['tcp://localhost:9042']);

        self::assertNull($config->user());
        self::assertNull($config->password());
    }

    public function testTcpOptions()
    {
        $config = new Config(['tcp://localhost:9042']);

        self::assertEquals(1, $config->tcpAttempts());
        self::assertEquals(0, $config->tcpTimeout());
        self::assertEquals(false, $config->tcpNoDelay());

        self::assertEquals(3, $config->tcpAttempts(3));
        self::assertEquals(10, $config->tcpTimeout(10));
        self::assertEquals(true, $config->tcpNoDelay(true));
    }

    public function testCassandraOptions()
    {
        $config = new Config(['tcp://localhost:9042']);

        self::assertEquals(false, $config->compatibility());
        self::assertEquals(Config::COMPRESSION_NONE, $config->compression());

        self::assertEquals(true, $config->compatibility(true));
        self::assertEquals([
            'CQL_VERSION' => '3.0.0',
            'NO_COMPACT'  => false,
        ], $config->options());

        if (\extension_loaded('snappy')) {
            self::assertEquals(Config::COMPRESSION_SNAPPY, $config->compression(Config::COMPRESSION_SNAPPY));
            self::assertEquals([
                'CQL_VERSION' => '3.0.0',
                'COMPRESSION' => Config::COMPRESSION_SNAPPY,
                'NO_COMPACT'  => false,
            ], $config->options());
        }

        if (\extension_loaded('lz4')) {
            self::assertEquals(Config::COMPRESSION_LZ4, $config->compression(Config::COMPRESSION_LZ4));
            self::assertEquals([
                'CQL_VERSION' => '3.0.0',
                'COMPRESSION' => Config::COMPRESSION_LZ4,
                'NO_COMPACT'  => false,
            ], $config->options());
        }
    }

    public function testParse()
    {
        $config = Config::parse('tcp://localhost:9042');

        self::assertEquals(['tcp://localhost:9042'], $config->hosts());
        self::assertEquals(null, $config->user());
        self::assertEquals(null, $config->password());

        $config = Config::parse('tcp://user:pass@localhost:9042');

        self::assertEquals(['tcp://localhost:9042'], $config->hosts());
        self::assertEquals('user', $config->user());
        self::assertEquals('pass', $config->password());

        $config = Config::parse('tcp://localhost:9042?tcp_nodelay=1&tcp_attempts=10&tcp_timeout=100');

        self::assertEquals(true, $config->tcpNoDelay());
        self::assertEquals(10, $config->tcpAttempts());
        self::assertEquals(100, $config->tcpTimeout());

        $config = Config::parse('tcp://localhost:9042?compression=none&compatibility=1');

        self::assertEquals(Config::COMPRESSION_NONE, $config->compression());
        self::assertEquals(true, $config->compatibility());
    }

    public function testParseMultiHost()
    {
        $config = Config::parse('tcp://localhost:9041,127.0.0.2,127.0.0.3:9043');

        self::assertEquals([
            'tcp://localhost:9041',
            'tcp://127.0.0.2:9042',
            'tcp://127.0.0.3:9043',
        ], $config->hosts());
    }

    public function testUnknownCompression()
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Unknown compression mechanism "gzip".');

        $config = new Config(['tcp://localhost:9042']);
        $config->compression('gzip');
    }

    public function testCompressionExtensionNotLoaded()
    {
        if (\extension_loaded('lz4')) {
            self::expectNotToPerformAssertions();

            return;
        }

        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Extension for compression mechanism "lz4" not loaded.');

        $config = new Config(['tcp://localhost:9042']);
        $config->compression(Config::COMPRESSION_LZ4);
    }
}
