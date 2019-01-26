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

namespace PHPinnacle\Cassis;

final class Config
{
    private const
        DEFAULT_HOST     = 'localhost',
        DEFAULT_PORT     = 9042,
        DEFAULT_KEYSPACE = null,
        DEFAULT_USER     = null,
        DEFAULT_PASS     = null
    ;

    private const OPTIONS = [
        'compression'   => null,
        'compatibility' => false,
        'tcp_timeout'   => 1000,
        'tcp_nodelay'   => false,
        'tcp_attempts'  => 2,
    ];

    private const
        COMPRESSION_LZ4    = 'lz4',
        COMPRESSION_SNAPPY = 'snappy'
    ;

    private const CQL_VERSION = '3.0.0';

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $keyspace;

    /**
     * @var int
     */
    private $tcpTimeout = 1;

    /**
     * @var bool
     */
    private $tcpNoDelay = false;

    /**
     * @var int
     */
    private $tcpAttempts = 2;

    /**
     * @var string
     */
    private $compression;

    /**
     * @var bool
     */
    private $compatibility;

    /**
     * @param string $host
     * @param int    $port
     * @param string $keyspace
     * @param string $user
     * @param string $pass
     */
    public function __construct(string $host, int $port, string $keyspace = null, string $user = null, string $pass = null)
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->keyspace = $keyspace ?: self::DEFAULT_KEYSPACE;
        $this->user     = $user ?: self::DEFAULT_USER;
        $this->pass     = $pass ?: self::DEFAULT_PASS;
    }

    /**
     * @param string $dsn
     *
     * @return self
     */
    public static function parse(string $dsn): self
    {
        $parts = \parse_url($dsn);

        \parse_str($parts['query'] ?? '', $query);

        $options = \array_replace(self::OPTIONS, $query);

        $self = new self(
            $parts['host'] ?? self::DEFAULT_HOST,
            $parts['port'] ?? self::DEFAULT_PORT,
            $parts['path'] ?? self::DEFAULT_KEYSPACE,
            $parts['user'] ?? self::DEFAULT_USER,
            $parts['pass'] ?? self::DEFAULT_PASS
        );

        $self->tcpAttempts = \filter_var($options['tcp_attempts'], FILTER_VALIDATE_INT);
        $self->tcpNoDelay  = \filter_var($options['tcp_nodelay'], FILTER_VALIDATE_BOOLEAN);
        $self->tcpTimeout  = \filter_var($options['tcp_timeout'], FILTER_VALIDATE_INT);

        $self->compression   = $options['compression'] ?? null;
        $self->compatibility = $options['compatibility'] ?? null;

        return $self;
    }

    /**
     * @return string
     */
    public function uri(): string
    {
        return \sprintf('tcp://%s:%d', $this->host, $this->port);
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function port(): int
    {
        return $this->port;
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function user(string $value = null): string
    {
        return \is_null($value) ? $this->user : $this->user = $value;
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function password(string $value = null): string
    {
        return \is_null($value) ? $this->pass : $this->pass = $value;
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function keyspace(string $value = null): string
    {
        return \is_null($value) ? $this->keyspace : $this->keyspace = $value;
    }

    /**
     * @param int|null $value
     *
     * @return int
     */
    public function tcpTimeout(int $value = null): int
    {
        return \is_null($value) ? $this->tcpTimeout : $this->tcpTimeout = $value;
    }

    /**
     * @param bool|null $value
     *
     * @return bool
     */
    public function tcpNoDelay(bool $value = null): bool
    {
        return \is_null($value) ? $this->tcpNoDelay : $this->tcpNoDelay = $value;
    }

    /**
     * @param int|null $value
     *
     * @return int
     */
    public function tcpAttempts(int $value = null): int
    {
        return \is_null($value) ? $this->tcpAttempts : $this->tcpAttempts = $value;
    }

    /**
     * @return bool|null
     */
    public function compatibility(): ?bool
    {
        return $this->compatibility;
    }

    /**
     * @return null|string
     */
    public function compression(): ?string
    {
        return $this->compression;
    }

    /**
     * @return array
     */
    public function options(): array
    {
        return \array_filter([
            'CQL_VERSION' => self::CQL_VERSION,
            //'COMPRESSION' => $this->compression,
            //'NO_COMPACT'  => \is_bool($this->compatibility) ? !$this->compatibility : null,
        ]);
    }
}
