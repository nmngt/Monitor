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
@define('MONITOR_MODE', 'http');

require __DIR__ . '/../vendor/autoload.php';

// NGT Monitor Application
$app = require_once __DIR__.'/../bootstrap.php';

// run
try {
    $app->runConfigChecks(true);
} catch (\Exception $e) {
    // Handle app's exceptions
    echo "<p>";
    echo "ERROR: ".$e->getMessage()."\n";
    echo "</p>";
}
