<?php

return [
    'default' => 'IDR',

    'currencies' => [
        'IDR' => [
            'symbol' => 'Rp',
            'position' => 'before',
            'decimals' => 0,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'space' => true,
        ],
        'USD' => [
            'symbol' => '$',
            'position' => 'before',
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'space' => false,
        ],
        'EUR' => [
            'symbol' => '€',
            'position' => 'before',
            'decimals' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'space' => false,
        ],
        'SGD' => [
            'symbol' => 'S$',
            'position' => 'before',
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'space' => false,
        ],
        'MYR' => [
            'symbol' => 'RM',
            'position' => 'before',
            'decimals' => 2,
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'space' => true,
        ],
    ],
];
