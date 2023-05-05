<?php
/**
 * Dezero common bootstrap file
 *
 * This file is used for web and console applications
 */

use dezero\helpers\ConfigHelper;
use yii\base\ErrorException;

// Get the last error at the earliest opportunity, so we can catch max_input_vars errors
// see https://stackoverflow.com/a/21601349/1688568
$lastError = error_get_last();

// AppType is required
$appType = $appType ?? 'web';

// ALIASES
// -----------------------------------------------------------------------------
Yii::setAlias('@app', DZ_BASE_PATH . DIRECTORY_SEPARATOR . 'app');
Yii::setAlias('@core', DZ_CORE_PATH);
Yii::setAlias('@dz', DZ_CORE_PATH . DIRECTORY_SEPARATOR . 'src');
Yii::setAlias('@dezero', DZ_CORE_PATH . DIRECTORY_SEPARATOR . 'src');

// CONFIGURATION FILES
// -----------------------------------------------------------------------------
$config = ConfigHelper::merge([
    DZ_CONFIG_PATH . "/app.php",
    // DZ_CONFIG_PATH . "/env/env.php",
    DZ_CONFIG_PATH . "/{$appType}.php",
    DZ_CONFIG_PATH . "/local/app.local.php"
]);

// Initialize the application
/** @var \yii\web\Application|yii\console\Application $app */
$app = Dz::createObject($config);

// If there was a max_input_vars error, kill the request before we start processing it with incomplete data
if ( $lastError && strpos($lastError['message'], 'max_input_vars') !== false )
{
    throw new ErrorException($lastError['message']);
}

// Import DzLog global function
require DZ_CORE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'DzLog.php';

return $app;
