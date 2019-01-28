<?php

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

        $gmp = @\gmp_init($value);

        if ($error = \error_get_last()) {
            throw new \InvalidArgumentException("Value \"{$value}\" not numeric.");
        }

        return $gmp;
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
