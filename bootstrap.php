<?php
/**
 * $Id:$
 * ---------------------------------------------------------------------------
 *  @version   $Revision:$
 *  @package   NGT\Monitor
 *  @author    Norman Georg-Tusel (norm@ngeorg.com)
 *  @link      https://georg-tusel.com/
 *  @copyright © ngt 2019, https://georg-tusel.com/
 * ---------------------------------------------------------------------------
 * ::::::::::::: vim: set noai noet ru nu ml fenc=utf-8 ts=4 sw=4 tw=0 ft=php:
 * ---------------------------------------------------------------------------
 */
@define('ABSPATH', __DIR__);
@define('MONITOR_START', microtime(true));

// set system date time
@date_default_timezone_set('Europe/Berlin');

// Test if we running on PHP7 or newer
@version_compare(PHP_VERSION, '7', '<') and exit('NGT\Monitor requires at least PHP 7 or newer.');

// set error reporting
error_reporting(E_ALL | E_STRICT);

// set error logging
@ini_set('error_log', __DIR__.'/storage/php_error.log');
@ini_set('log_errors', 'On');
@ini_set('display_errors', 'On');

/*
|--------------------------------------------------------------------------
| Development Helper ;)
|--------------------------------------------------------------------------
|
*/

function d($val, $die = false)
{
    dump($val);
    if ($die) {
        die("\n---------------> DIE <---------------\n");
    }
}

/*
|--------------------------------------------------------------------------
| Create Exception Handler
|--------------------------------------------------------------------------
|
*/

// Whoops Exception Handler
// $whoops = new \Whoops\Run;
// $whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
// $whoops->register();

/*
|--------------------------------------------------------------------------
| Create Registry
| (the registry is hacked/missused for status messages in this application)
|--------------------------------------------------------------------------
|
*/
use NGT\Registry;
Registry::__init();

/*
|--------------------------------------------------------------------------
| Create Application Handler
|--------------------------------------------------------------------------
|
*/

$app = new NGT\Monitor\App(realpath(__DIR__));

return $app;
