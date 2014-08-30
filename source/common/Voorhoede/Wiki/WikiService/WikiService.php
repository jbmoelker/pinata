<?php

namespace Voorhoede\Wiki\WikiService;

use GlobIterator;
use Voorhoede\Wiki\Article\Article;
use Voorhoede\Wiki\Search\SearchIndex;
use Voorhoede\Wiki\Search\SearchQuery;

class WikiService {
    protected $contentDir;
    protected $cacheDir;
    protected $useCache = true;

    public function __construct($config)
    {
        $this->contentDir = $config['contentDir'];
        $this->cacheDir   = $config['cacheDir'];
    }

    function useCache($useCache)
    {
        $this->useCache = $useCache;
        return $this;
    }

    function getArticleFiles($names)
    {
        if(!isset($names)){
            $names = array();
            // iterator: http://www.sitepoint.com/list-files-and-directories-with-php/
            $iterator = new GlobIterator($this->contentDir.'*.md');
            foreach($iterator as $entry) {
                $names[] = basename($entry->getFilename(), '.md');
            }
        }

        return $names;
    }

    public function article($name)
    {
        $article = new Article($name);
        $article
            ->setContentDir($this->contentDir)
            ->setCacheDir($this->cacheDir .'content/')
            ->useCache($this->useCache);
        return $article;
    }

    public function articles($names = null)
    {
        $articles = array();

        if(!isset($names)){
            $names = array();
            // iterator: http://www.sitepoint.com/list-files-and-directories-with-php/
            $iterator = new GlobIterator($this->contentDir.'*.md');
            foreach($iterator as $entry) {
                $names[] = basename($entry->getFilename(), '.md');
            }
        }

        foreach($names as $name){
            $article = $this->article($name)->asArray();
            $articles[] = $article;
        }

        return $articles;
    }

    public function publicArticles($names = null)
    {
		$articles = array_filter($this->articles($names), function ($article) {
			return $article['isPublic'];
		});

        return $articles;
    }

    public function find($query)
    {
        $hits = SearchQuery::find($query);
        $names = array();
        foreach($hits as $hit){
            $names[] = $hit->name;
        }
        return array(
            'hits'      => $hits,
        );
    }

    public function publicFind($query)
    {
        $hits = SearchQuery::find($query);
        $names = array();
        foreach($hits as $hit){
            $names[] = $hit->name;
        }
        return array(
            'hits'      => $hits,
        );
    }
}