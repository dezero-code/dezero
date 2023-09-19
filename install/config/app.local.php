<?php
/**
 * main-local.php
 *
 * This file should have the LOCAL configuration settings that will be merged to the main.php
 *
 * This configurations should be only related to your development machine
 */

return [
    'name' => getenv('SITE_NAME'),

    'components' => [
        // Database connection
        'db' => [
            'class' => 'dezero\db\Connection',
            'commandClass' => 'dezero\db\Command',
            'commandMap' => [
                'mysql' => 'dezero\db\mysql\Column'
            ],
            'schemaMap' => [
                'mysql' => 'dezero\db\mysql\Schema'
            ],

            'dsn' => getenv('DB_DRIVER') . ':host=' . getenv('DB_SERVER') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            'username' => getenv('DB_USER'),     // 'root',
            'password' => getenv('DB_PASSWORD'), // 'root00'
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'emulatePrepare' => true,

            'enableLogging' => Dz::isDev(),       // Whether to enable logging of database queries.
            'enableProfiling' => Dz::isDev(),     // Whether to enable profiling of opening database connection and database queries.
            'enableSchemaCache' => ! Dz::isDev(),   // Whether to enable schema caching.
        ],
    ]
];
