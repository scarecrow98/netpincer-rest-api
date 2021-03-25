<?php

return [
    'defaults' => [
        'guard' => 'user',
        'passwords' => 'users',
    ],

    'guards' => [
        'user' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
        'partner' => [
            'driver' => 'jwt',
            'provider' => 'partners',
        ],
        'courier' => [
            'driver' => 'jwt',
            'provider' => 'couriers',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ],
        'partners' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Partner::class
        ],
        'couriers' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Courier::class
        ]
    ]
];