<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        'company' => [
            'driver'   => 'session',
            'provider' => 'companies',
        ],

        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],

        'api' => [
            'driver'   => 'token',
            'provider' => 'users',
            'hash'     => true,   // since you’re hashing with sha256 before saving
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\User::class,   // if Laravel 8+, update to App\Models\User::class
        ],

        'companies' => [
            'driver' => 'eloquent',
            'model'  => App\Company::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Admin::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_resets',
            'expire'   => 180,
        ],

        'companies' => [
            'provider' => 'companies',
            'table'    => 'company_password_resets',
            'expire'   => 180,
        ],

        'admins' => [
            'provider' => 'admins',
            'table'    => 'admin_password_resets',
            'expire'   => 60,
        ],
    ],

];
