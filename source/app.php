<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Voorhoede\Wiki\WikiService\WikiService;

return function($environment)
{
	$app = new Application();
	$app->register(new UrlGeneratorServiceProvider());
	$app->register(new ValidatorServiceProvider());
	$app->register(new ServiceControllerServiceProvider());
	$app->register(new TwigServiceProvider());

	require __DIR__ .'/../config/'. $environment .'.php';

	// Custom filters
	require __DIR__ .'/common/Voorhoede/Wiki/Filters/externalReadableUrl.php';
	$app['twig']->addFilter($filterExternalReadableUrl);

	require __DIR__ .'/common/Voorhoede/Wiki/Filters/externalUrl.php';
	$app['twig']->addFilter($filterExternalUrl);

	require __DIR__ .'/common/Voorhoede/Wiki/Filters/internalReadableUrl.php';
	$app['twig']->addFilter($filterInternalReadableUrl);


	$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
		$twig->addGlobal('projectTitle', $app['projectTitle']);
		$twig->addGlobal('projectRepository', $app['projectRepository']);
		return $twig;
	}));

	$app['wiki'] = $app->share(function($app) {
		$wiki = new WikiService(array(
			'contentDir' => $app['contentDir'],
			'cacheDir'   => $app['cacheDir'],
		));
		$wiki->useCache(!$app['debug']);
		return $wiki;
	});

	foreach($app['enabledViews'] as $view) {
		require_once sprintf('modules/views/%1$s/%1$s.php', $view);
	}

	return $app;
};
