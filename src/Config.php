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
        DEFAULT_SCHEME = 'tcp',
        DEFAULT_HOST   = 'localhost',
        DEFAULT_PORT   = 9042
    ;

    private const CQL_VERSION = '3.0.0';
    
    const
        COMPRESSION_NONE   = 'none',
        COMPRESSION_LZ4    = 'lz4',
        COMPRESSION_SNAPPY = 'snappy'
    ;

    /**
     * @var string[]
     */
    private $hosts;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var int
     */
    private $tcpTimeout = 0;

    /**
     * @var bool
     */
    private $tcpNoDelay = false;

    /**
     * @var int
     */
    private $tcpAttempts = 1;

    /**
     * @var string
     */
    private $compression = self::COMPRESSION_NONE;

    /**
     * @var bool
     */
    private $compatibility = false;

    /**
     * @param string[] $hosts
     * @param string   $user
     * @param string   $pass
     */
    public function __construct(array $hosts, string $user = null, string $pass = null)
    {
        $this->hosts = $hosts;
        $this->user  = $user;
        $this->pass  = $pass;
    }

    /**
     * @param string $dsn
     *
     * @return self
     */
    public static function parse(string $dsn): self
    {
        $parts = \parse_url($dsn);

        $scheme = $parts['scheme'] ?? self::DEFAULT_SCHEME;
        $hosts  = \explode(',', $parts['host'] ?? self::DEFAULT_HOST);
        $uris   = [];

        if (isset($parts['port'])) {
            \end($hosts);

            $key = \key($hosts);

            $hosts[$key] = $hosts[$key] . ':' . $parts['port'];

            \reset($hosts);
        }

        foreach ($hosts as $host) {
            $hostAndPort = \explode(':', $host);

            $uris[] = \sprintf('%s://%s:%d', $scheme, $hostAndPort[0], (int) ($hostAndPort[1] ?? self::DEFAULT_PORT));
        }

        $self = new self($uris, $parts['user'] ?? null, $parts['pass'] ?? null);

        \parse_str($parts['query'] ?? '', $options);

        if (isset($options['tcp_nodelay'])) {
            $self->tcpNoDelay  = \filter_var($options['tcp_nodelay'], FILTER_VALIDATE_BOOLEAN);
        }

        if (isset($options['tcp_attempts'])) {
            $self->tcpAttempts = \filter_var($options['tcp_attempts'], FILTER_VALIDATE_INT) ?: 0;
        }

        if (isset($options['tcp_timeout'])) {
            $self->tcpTimeout = \filter_var($options['tcp_timeout'], FILTER_VALIDATE_INT) ?: 0;
        }

        if (isset($options['compression']) && \is_string($options['compression'])) {
            $self->compression($options['compression']);
        }

        if (isset($options['compatibility'])) {
            $self->compatibility(\filter_var($options['compatibility'], FILTER_VALIDATE_BOOLEAN));
        };

        return $self;
    }

    /**
     * @return string[]
     */
    public function hosts(): array
    {
        return $this->hosts;
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function user(string $value = null): ?string
    {
        return \is_null($value) ? $this->user : $this->user = $value;
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function password(string $value = null): ?string
    {
        return \is_null($value) ? $this->pass : $this->pass = $value;
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
     * @param bool|null $value
     *
     * @return bool
     */
    public function compatibility(bool $value = null): bool
    {
        return \is_null($value) ? $this->compatibility : $this->compatibility = $value;
    }

    /**
     * @param string|null $value
     *
     * @return string
     */
    public function compression(string $value = null): string
    {
        if (\is_null($value)) {
            return $this->compression;
        }

        if (!\in_array($value, [
            self::COMPRESSION_NONE,
            self::COMPRESSION_LZ4,
            self::COMPRESSION_SNAPPY,
        ])) {
            throw Exception\ConfigException::unknownCompressionMechanism($value);
        }

        if ($value !== self::COMPRESSION_NONE && !\extension_loaded($value)) {
            throw Exception\ConfigException::compressionExtensionNotLoaded($value);
        }

        return $this->compression = $value;
    }

    /**
     * @return array
     */
    public function options(): array
    {
        $options = [
            'CQL_VERSION' => self::CQL_VERSION,
        ];

        if ($this->compression !== self::COMPRESSION_NONE) {
            $options['COMPRESSION'] = $this->compression;
        }
        
        if ($this->compatibility) {
            $options['NO_COMPACT'] = false;
        }
        
        return $options;
    }
}
