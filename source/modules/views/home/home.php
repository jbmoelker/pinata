<?php

$app->get('/', function () use ($app) {

    return $app['twig']->render('views/home/home.html', array(
    	'titleTopics' => 'Topics',
        'title' => 'Pinata search OMGWTFBBQ',
    ));
})
->bind('home');
