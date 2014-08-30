<?php

ini_set('display_errors', 0);

$projectDir = __DIR__.'/../';

require_once $projectDir . 'vendor/autoload.php';

$bootstrap = require $projectDir . 'source/app.php';
$app = $bootstrap('production');
$app->run();