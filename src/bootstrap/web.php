<?php
/**
 * Dezero WEB bootstrap file
 *
 * This file holds the first configuration settings of your application
 */

// Make sure they're running PHP 7.3
if ( PHP_VERSION_ID < 70300 )
{
    exit('Dezero Framework requires PHP 7.3 or later.');
}

// Check for this early because DZ Framework uses it before the requirements checker gets a chance to run.
if ( ! extension_loaded('mbstring') || ini_get('mbstring.func_overload') != 0 )
{
    exit('Dezero Framework requires the <a href="http://php.net/manual/en/book.mbstring.php" rel="noopener" target="_blank">PHP multibyte string</a> extension in order to run. Please talk to your host/IT department about enabling it on your server.');
}

// PHP environment normalization
// -----------------------------------------------------------------------------

mb_detect_order('auto');

// Normalize how PHP's string methods (strtoupper, etc) behave.
setlocale(
    LC_CTYPE,
    'C.UTF-8',      // libc >= 2.13
    'C.utf8',       // different spelling
    'en_US.UTF-8',  // fallback to lowest common denominator
    'en_US.utf8'    // different spelling for fallback
);

// Set default timezone to UTC
date_default_timezone_set('UTC');


// Kint configuration
// -----------------------------------------------------------------------------
if ( DZ_ENVIRONMENT == 'prod' )
{
    Kint::$enabled_mode = false;
}
else
{
    Kint\Renderer\RichRenderer::$folder = false;

    // Global function "dd"
    if ( ! function_exists('dd') )
    {
        function dd()
        {
            $argv = func_get_args();
            call_user_func_array(['Kint', 'dump'], $argv);
            die;
        }
    }
}

// Load Dezero Framework
// -----------------------------------------------------------------------------
$appType = 'web';
return require __DIR__ . '/common.php';
