<?php

// Zend 1.12 Search (no docs for v2 yet): http://framework.zend.com/manual/1.12/en/zend.search.lucene.index-creation.html
// http://abouthalf.com/development/adding-zend-search-lucene-to-a-silex-project/

namespace Voorhoede\Wiki\Search;

use Voorhoede\Wiki\Article\Article;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Document\HTML as HtmlDocument;
use ZendSearch\Lucene\Lucene;
use Symfony\Component\DomCrawler\Crawler;
use Curl\Curl;

class SearchIndex
{
    protected $contentSourceDir;
    protected $contentCacheDir;
    protected $searchCacheDir;
    protected $output;
    
    public function log($string)
    {
        if ($this->output != null)
        {
            $this->output->writeLn($string);
        }
    }
    
    public function __construct($output = null)
    {
        $this->output = $output;
        $projectDir = __DIR__.'/../../../../../';
        $this->contentSourceDir = $projectDir .'source/content/';
        $this->contentCacheDir  = $projectDir .'cache/content/';
        $this->searchCacheDir   = $projectDir .'cache/search/';
    }

    public function create()
    {
        // Create index
        $index = Lucene::create($this->searchCacheDir);
        
        
        $urls = array(
            "http://www.metro.us/newyork/topics/news/feed/",
            "http://www.metro.us/newyork/topics/entertainment/feed/",
            "http://www.metro.us/newyork/topics/sports/feed/", 
            "http://www.metro.us/newyork/topics/lifestyle/feed/", 
           // "http://www.metro.us/newyork/topics/games/feed/"
        );
        
        foreach($urls as $url)
        {
            $this->IndexUrl($url, $index);
        }
    }
    
    public function IndexUrl($url, $index)
    {
        $curl = new \Curl\Curl();
        $curl->setOpt(CURLOPT_ENCODING, "gzip");
        $curl->get($url);
        $xmlfeed =  $curl->response;
        
        
        $crawler = new Crawler($xmlfeed);
        /* @var $item \DOMElement */
        foreach ($crawler->filter("item") as $item)
        {
            $document = $this->createDocument($item);
            $index->addDocument($document);
        }
    }

    public function update($name)
    {
    }

    public function createDocument(\DOMElement $item)
    {
        
        $title = $item->getElementsByTagName("title")->item(0)->nodeValue;
        $this->log("Adding item:" . $title);
        $content = $item->getElementsByTagName("description")->item(0)->nodeValue;
        $link = $item->getElementsByTagName("link")->item(0)->nodeValue;
        $category = array();
        foreach ($item->getElementsByTagName("category") as $item)
        {
            $category[] = $item->nodeValue;
        }
        $document = HtmlDocument::loadHTML($content);

        $document->addField(Field::keyword('name', $title ));
        $document->addField(Field::keyword('title', $title));
        $document->addField(Field::keyword('category', implode(', ', $category) ));
        $document->addField(Field::text('url', $link));

        return $document;
    }
}