<?php

use App\Models\User;

return [
    'defaults' => [
        'guard' => 'web',  // Toujours 'web' par défaut
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        // Sanctum n'a pas besoin d'être déclaré ici,
        // il s'enregistre automatiquement via son ServiceProvider
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];