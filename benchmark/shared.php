<?php

function random_string(int $length): string
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $count = \strlen($chars);

    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $chars[\rand(0, $count - 1)];
    }

    return $string;
}

function random_date(): \DateTimeInterface
{
    /** @noinspection PhpUnhandledExceptionInspection */
    return new \DateTimeImmutable;
}

function random_tags(int $count, int $length): array
{
    $tags = [];

    for ($i = 0; $i < $count; $i++) {
        $tags[] = random_string($length);
    }

    return $tags;
}

return [
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
