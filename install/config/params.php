<?php
/**
 * Parameters shared by all applications.
 */
return [
    'adminEmail' => getenv('SITE_EMAIL'),
    'basePath' => getenv('BASE_PATH'),
    'baseUrl' => getenv('SITE_URL'),
    'temporaryPath' => false,

    // Session timeou (just one day)
    'session_timeout' => 86400,

    // Variables yo EXPORT int Javascript as globals
    'js_globals' => [
        'base_url' => getenv('SITE_URL'),
    ],

    // Databse BACKUP Command
    'backup_command' => '/usr/bin/mysqldump',
    'restore_command' => '/usr/bin/mysql',
    'zip_command' => '/usr/bin/zip',
    'git_command' => '/usr/bin/git',

    // Mail custom params
    'mail' => [
        'is_enabled' => true,
        'is_test' => true,
        'test_email' => 'test@dezero.es',
    ],
];
