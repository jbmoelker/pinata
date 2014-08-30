<?php

use Symfony\Component\Debug\Debug;

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
	|| preg_match('/^(development|acceptance)\./i', $_SERVER['SERVER_NAME'])
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$projectDir = __DIR__.'/../';

require_once $projectDir . 'vendor/autoload.php';

Debug::enable();

$bootstrap = require $projectDir . 'source/app.php';
$app = $bootstrap('development');
$app->run();
