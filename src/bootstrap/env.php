<?php
/**
 * Environment bootstrap file
 */

// Load dotenv configuration
if ( file_exists(DZ_BASE_PATH . '/.env') && class_exists(Dotenv\Dotenv::class) )
{
    // (new Dotenv\Dotenv(DZ_BASE_PATH))->load();

    // By default, this will allow .env file values to override environment variables
    // with matching names. Use `createUnsafeImmutable` to disable this.
    Dotenv\Dotenv::createUnsafeMutable(DZ_BASE_PATH)->safeLoad();
}

// Set constant
define('DZ_ENVIRONMENT', getenv('ENVIRONMENT') ?: 'prod');

// @see https://www.php.net/manual/es/function.error-reporting.php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// LIVE environment configuration
if ( DZ_ENVIRONMENT == 'prod' )
{
    ini_set('display_errors', '0');
    defined('YII_DEBUG') || define('YII_DEBUG', false);
    defined('YII_ENV') || define('YII_ENV', 'prod');
}

// DEV environment configuration
else
{
    ini_set('display_errors', '1');
    defined('YII_DEBUG') || define('YII_DEBUG', true);
    defined('YII_ENV') || define('YII_ENV', 'dev');
}
