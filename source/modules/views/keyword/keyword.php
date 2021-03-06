<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Voorhoede\Wiki\Search\SearchQuery;
use \Pinata\Keyword\KeywordGenerator;
use Linguistadores\Pinata\Translator\Translator;


$app->get('/keyword', function (Request $request) use ($app) {
   $url = $request->get('url', "");
   $content = $request->get('content',null);
   
   if ($content == null)
   {
     $curl = new \Curl\Curl();
       //$curl->setOpt(CURLOPT_ENCODING, "gzip");
     $curl->get($url);
     $content = $curl->response;
   }   
   
   $generator = new KeywordGenerator($app["cacheDir"], $app["javaApp"]);
   $stems = array();
   $keywords = $generator->GenerateKeywords($content);
   $limit = 10;
   $i = 0;
   
   $keywordsTranslated = [];
   $translator = new Translator("en", "nl");
   
   
   foreach ($keywords as $keyWord) {
       $stems[] = $keyWord["stem"]."^".$keyWord["frequency"];
       $term = $keyWord["terms"][0];
       $wordWithTranslation = array(
           "term" => $term,
           "translation" => $translator->translate($term),
          "frequency" => $keyWord["frequency"],
           "stem" => $keyWord["stem"]
       );
       $keywordsTranslated[] = $wordWithTranslation;
       if ($i++ >= $limit)
       {
           break;
       }
       
       
   } 
   
   $searchString = join(" ", $stems);
   
   $hits = $app['wiki']->publicFind($searchString)["hits"];
   
   $related = array();
   foreach ($hits as $hit)
    {
       $hitArray = array (
          "url" => $hit->url,
          "title" => $hit->title,
           "score" => round($hit->score*100). "%"
        );
        $related[] = $hitArray;
    }
   
    
    
    $output = array(
        //"keywords" => $keywords,
        "search_string" => $searchString,
        "related"  => $related,
        "keywords" => $keywordsTranslated
    );
    if ($request->get('format',"html") == "json")
    {
        return new Response(json_encode($output));
    }
    
    $html =  $app['twig']->render('views/keyword/keyword.html', array(
        'output'  => $output,
    ));
    
    if ($request->get("callback",false) !== false)
    {
        return new Response(
                sprintf("%s(%s);",$request->get('callback'), json_encode(array("html" => $html))),
                200,
                array('Content-Type' => 'application/javascript')
        );
    }
    
    return new Response($html);

})->bind('keyword');

$app->get('/keyword/api/{query}', function (Request $request, $query) use ($app) {

    //$query = $reqsuest->query->get('query');
    $hits = $app['wiki']-publicFind(sprintf("%s~",$query))['hits'];
    
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