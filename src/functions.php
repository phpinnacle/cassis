<?php
/**
 * This file is part of PHPinnacle/Cassis.
 *
 * (c) PHPinnacle Team <dev@phpinnacle.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @noinspection PhpComposerExtensionStubsInspection
 */

if (!\function_exists('is_assoc')) {
    function is_assoc(array $values): bool
    {
        return \array_keys($values) !== \range(0, \count($values) - 1);
    }
}

if (!\function_exists('bigint_init')) {
    function bigint_init(string $value): \GMP
    {
        \error_clear_last();

        /** @var \GMP $gmp */
        $gmp = @\gmp_init($value);

        if ($error = \error_get_last()) {
            throw new \InvalidArgumentException("Value \"{$value}\" not numeric.");
        }

        return $gmp;
    }
}

if (!\function_exists('bigint_strval')) {
    function bigint_strval(\GMP $gmp): string
    {
        return \gmp_strval($gmp);
    }
}

if (!\function_exists('bigint_import')) {
    function bigint_import(string $bytes): \GMP
    {
        \error_clear_last();

        $gmp = @\gmp_import($bytes);

        if ($error = \error_get_last()) {
            throw new \InvalidArgumentException("Invalid bytes number data.");
        }

        return $gmp;
    }
}

if (!\function_exists('bigint_export')) {
    function bigint_export(\GMP $gmp): string
    {
        return \gmp_export($gmp);
    }
}
