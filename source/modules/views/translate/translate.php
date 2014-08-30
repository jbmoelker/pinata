<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Linguistadores\Pinata\Translator\Translator;

$app->get('/translate/{sourceLang}/{targetLang}/{word}', function (Request $request, $sourceLang, $targetLang, $word) use ($app) {

    $translator = new Translator($sourceLang, $targetLang);

    return $app['twig']->render('views/translate/translate.html', array(
        'title' => sprintf('Translation from `%s` to `%s` for `%s`', $sourceLang, $targetLang, $word),
        'sourceLang' => $sourceLang,
        'targetLang' => $targetLang,
        'word' => $word,
        'translations' => $translator->translate($word),
    ));

})->bind('translate');