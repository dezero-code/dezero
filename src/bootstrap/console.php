<?php
/**
 * Dezero CONSOLE bootstrap file
 *
 * This file holds the first configuration settings of your application
 */

use yii\console\ExitCode;

// Make sure they're running PHP 7.3
if ( PHP_VERSION_ID < 70300 )
{
    echo 'Dezero Framework requires PHP 7.3 or later.';
    exit(ExitCode::UNSPECIFIED_ERROR);
}

// Make sure $_SERVER['SCRIPT_FILENAME'] is set
if ( ! isset($_SERVER['SCRIPT_FILENAME']) )
{
    $trace = debug_backtrace(0);
    if ( ($first = end($trace)) !== false && isset($first['file']) )
    {
        $_SERVER['SCRIPT_FILENAME'] = $first['file'];
    }
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



// Load Dezero Framework
// -----------------------------------------------------------------------------
$appType = 'console';
return require __DIR__ . '/common.php';
