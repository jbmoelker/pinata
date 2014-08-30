<?php

// http://framework.zend.com/manual/2.0/en/modules/zendsearch.lucene.searching.html#
// http://abouthalf.com/development/adding-zend-search-lucene-to-a-silex-project/

namespace Voorhoede\Wiki\Search;

use Exception;
use ZendSearch\Lucene\Exception as SearchException;
use ZendSearch\Lucene\Lucene;

class SearchQuery
{
    const NO_INDEX_MESSAGE = 'No search index available. To generate search index run <kbd>$ php tasks/console.php wiki:index</kbd>';

    static public function find($query)
    {
        $projectDir = __DIR__.'/../../../../../';
        $contentSourceDir = $projectDir .'source/content/';
        $contentCacheDir  = $projectDir .'cache/content/';
        $searchCacheDir   = $projectDir .'cache/search/';

        if(!file_exists($searchCacheDir)) {
            throw new Exception(static::NO_INDEX_MESSAGE);
        }

        $index = Lucene::open($searchCacheDir);

        $hits = $index->find($query);

        return $hits;
    }
}