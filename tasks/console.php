#!/usr/bin/env php
<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Voorhoede\Wiki\Search\IndexTask;
use Voorhoede\Wiki\Search\QueryTask;
use Linguistadores\Pinata\Translator\TranslateTask;

$console = new Application();
$console->add(new IndexTask);
$console->add(new QueryTask);
$console->add(new TranslateTask);
$console->run();