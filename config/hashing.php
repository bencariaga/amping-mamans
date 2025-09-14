<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default hash driver that will be used by the
    | framework to hash passwords and other secure values. A sensible
    | default using the Bcrypt algorithm is provided out of the box.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => env('HASH_DRIVER', 'bcrypt'),

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used
    | when hashing passwords using the Bcrypt algorithm. These will
    | be passed directly to PHP's password_hash function.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used
    | when hashing passwords using the Argon2 algorithm. These will be
    | passed directly to PHP's password_hash function.
    |
    */

    'argon' => [
        'memory'  => env('ARGON_MEMORY', 65536),
        'threads' => env('ARGON_THREADS', 1),
        'time'    => env('ARGON_TIME', 4),
    ],

];
