<?php

// configure your app for the production environment

$app['projectTitle'] = 'Pinata Hackatron';
$app['projectRepository'] = 'https://bitbucket.org/voorhoede/voorhoede-wiki/';

$app['contentDir'] 	= __DIR__.'/../source/content/';
$app['cacheDir'] 	= __DIR__.'/../cache/';
$app['javaApp'] 	= realpath(__DIR__.'/../backend/target/runnable.jar');


$app['twig.path'] = array(__DIR__.'/../source/modules');
$app['twig.options'] = array('cache' => $app['cacheDir'] .'twig');

$app['enabledViews'] = array(
     'error',
     'home',
     'search',
     'translate',
     'keyword'
 );
