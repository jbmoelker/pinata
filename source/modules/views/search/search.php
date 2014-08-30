<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Voorhoede\Wiki\Search\SearchQuery;


$app->get('/search', function (Request $request) use ($app) {

    $query = $request->query->get('query');
    $hits = $app['wiki']->publicFind(sprintf("%s~",$query))['hits'];

    return $app['twig']->render('views/search/search.html', array(
        'title'     => sprintf('Articles matching `%s`', $query),
        'hits'  => $hits,
        'query'     => $query,
    ));

})->bind('search');

$app->get('/search/api/{query}', function (Request $request, $query) use ($app) {

    //$query = $request->query->get('query');
    $hits = $app['wiki']->publicFind(sprintf("%s~",$query))['hits'];
    
    $output = array();
    foreach ($hits as $hit)
    {
        $hitArray = array (
          "url" => $hit->url,
          "title" => $hit->title
        );
        $output[] = $hitArray;
    }
    return new Response(sprintf("%s(%s);",$request->get('callback'), json_encode($output)));
})->bind('searchapi');


$app->get('/open-search-desc.xml', function () use ($app) {

    return new Response($app['twig']->render(
        'views/search/open-search-desc.xml', array()),
        200,
        array('Content-Type' => 'application/xml')
    );

})->bind('open-search-desc');