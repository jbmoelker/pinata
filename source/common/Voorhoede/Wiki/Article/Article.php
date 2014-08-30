<?php

namespace Voorhoede\Wiki\Article;

use \Michelf\MarkdownExtra;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

date_default_timezone_set('Europe/Amsterdam');

class Article {

	const EXT_MARKDOWN  = '.md';
	const EXT_YAML      = '.yml';
	const EXT_HTML      = '.html';
	const DATE_FORMAT   = 'Y-m-d H:i';

	protected $name;
	protected $useCache = true;
	protected $isCached;
	protected $contentDir;
	protected $cacheDir;
	protected $filenameOriginal;
	protected $filenameCachedYml;
	protected $filenameCachedHtml;
	protected $content;
	protected $html;
	protected $meta;

	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	function useCache($useCache)
	{
		$this->useCache = $useCache;
		return $this;
	}

	function setContentDir($contentDir)
	{
		$this->contentDir = $contentDir;
		$this->filenameOriginal = $contentDir . $this->name . static::EXT_MARKDOWN;
		return $this;
	}

	function setCacheDir($cacheDir)
	{
		$this->cacheDir = $cacheDir;
		$this->filenameCachedYml  = $cacheDir . $this->name . static::EXT_YAML;
		$this->filenameCachedHtml = $cacheDir . $this->name . static::EXT_HTML;
		return $this;
	}

	function exists()
	{
		return file_exists($this->filenameOriginal);
	}

	function isCached()
	{
		if(!isset($this->isCached)) {
			$this->isCached = file_exists($this->filenameCachedYml) && file_exists($this->filenameCachedHtml);
		}
		return $this->isCached;
	}

	/**
	 * Checks and returns true if 'private' is in article state list.
	 * @return boolean
	 */
	public function isPrivate()
	{
		$meta = $this->getMeta();
		return in_array('private', array_map('strtolower', $meta['state']) );
	}

	/**
	 * Checks and returns true if 'draft' is in article state list.
	 * @return boolean
	 */
	public function isDraft()
	{
		$meta = $this->getMeta();
        return in_array('draft', array_map('strtolower', $meta['state']) );
	}

	public function isPublic()
	{
		return !$this->isDraft() && !$this->isPrivate();
	}

	function getContent()
	{
		if(!isset($this->content)) {
			$this->content = file_get_contents($this->filenameOriginal);
		}
		return $this->content;
	}

	function parseMeta()
	{
		$content = $this->getContent();
		$meta = array();

		// assume file starts with metadata between triple hyphens ---
		if(substr($content, 0, 3) == '---'){
			$parts = explode('---', $content, 3);
			$metaBlock 	= $parts[1];
			$metaLines 	= array_filter( explode("\n", $metaBlock), "strlen");
			foreach($metaLines as &$line) {
				$line = trim($line);
			}
			$metaYml = join("\n", $metaLines);
			$meta = Yaml::parse($metaYml);
		}

		// set defaults for missing metadata
		if (!isset($meta['title'])) {
			$html = $this->getHtml();
			$crawler = new Crawler($html);
			$meta['title'] = $crawler->filter('h1')->first()->text();
		}
		if (!isset($meta['description'])) {
			$html = $this->getHtml();
			$crawler = new Crawler($html);
			$paragraphs = $crawler->filter('p');
			$index = 0;
			$length = sizeof($paragraphs);
			$text = '';
			while(strlen($text) < 20 && $index < $length){
				$text .= strip_tags( $paragraphs->eq($index)->text() );
				$index++;
			}
			if(strlen($text) > 100){
				$text = substr($text, 0, 100) . '...';
			}
			$meta['description'] = $text;
		}

		if (!isset($meta['dateCreated'])) {
			$meta['dateCreated'] = date(static::DATE_FORMAT, filectime($this->filenameOriginal));
		}
		if (!isset($meta['dateUpdated'])) {
			$meta['dateUpdated'] = date(static::DATE_FORMAT, filemtime($this->filenameOriginal));
		}
		if (!isset($meta['tags'])) { $meta['tags'] = array(); }
		if (!isset($meta['authors'])) { $meta['authors'] = array(); }
		if (!isset($meta['image'])) { $meta['image'] = null; }
		if (!isset($meta['state'])) { $meta['state'] = array(); }

		return $meta;
	}

	function parseHtml()
	{
		$content = $this->getContent();
		// assume file starts with metadata between triple hyphens ---
		if(substr($content, 0, 3) == '---'){
			$parts = explode('---', $content, 3);
			$content = $parts[2];
		}

		// create and clean html
		$parser = new MarkdownExtra;
		$html = $parser->transform($content);
		$html = $this->cleanHtml($html);
		$html = $this->indexHtml($html);

		return $html;
	}

	private function cleanHtml($html)
	{
        $html = str_replace('<p><figure></p>', '<figure>', $html);
        $html = str_replace('<p></figure></p>', '</figure>', $html);
		return $html;
	}

	private function indexHtml($html)
	{
		$dom = HtmlDomParser::str_get_html($html);
		$headings = $dom->find('h1, h2, h3, h4, h5, h6');

		foreach($headings as &$heading) {
			$text	= $heading->plaintext;
			$slug	= $this->slugify($text);
			$heading->outertext = sprintf('<a id="%s" href="#%s">%s</a>', $slug, $slug, $heading->outertext);
		}
		$indexedHtml = $dom->save();
		// clear and unset to prevent memory leak
		$dom->clear();
		unset($dom);
		return $indexedHtml;
	}

	/**
	 * based on: https://github.com/KnpLabs/DoctrineBehaviors/blob/master/src/Knp/DoctrineBehaviors/Model/Sluggable/Sluggable.php#L96
	 * @param string $text	text to create slug from
	 * @return string		slug
	 */
	function slugify($text) {
		$slugDelimiter = '-';
		$urlized = strtolower( trim( preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '',
				iconv('UTF-8', 'ASCII//TRANSLIT', $text) ), $slugDelimiter )
			);
		return preg_replace("/[\/_|+ -]+/", $slugDelimiter, $urlized);
	}

	function getHtml()
	{
		if(!isset($this->html)){
			if($this->useCache && $this->isCached()) {
				$this->html = file_get_contents($this->filenameCachedHtml);
			} else {
				$this->html = $this->parseHtml();
				$filesystem = new Filesystem();
				$filesystem->dumpFile($this->filenameCachedHtml, $this->html);
			}
		}
		return $this->html;
	}

	function getMeta()
	{
		if(!isset($this->meta)){
			if($this->useCache && $this->isCached()) {
				$metaYml = file_get_contents($this->filenameCachedYml);
				$this->meta = Yaml::parse($metaYml);
			} else {
				$this->meta = $this->parseMeta();
				$filesystem = new Filesystem();
				$filesystem->dumpFile($this->filenameCachedYml, Yaml::dump($this->meta));
			}
		}
		return $this->meta;
	}

	function getTitle()
	{
		$meta = $this->getMeta();
		return $meta['title'];
	}

	function asArray()
	{
		$meta = $this->getMeta();

		$article = array(
			'name'          => $this->name,
			'title'         => $meta['title'],
			'description'   => $meta['description'],
			'dateCreated'   => $meta['dateCreated'],
			'dateUpdated'   => $meta['dateUpdated'],
			'authors'       => $meta['authors'],
			'tags'          => $meta['tags'],
			'image'         => $meta['image'],
			'state'         => $meta['state'],
			'meta'          => $meta,
			'html'          => $this->getHtml(),
			'isDraft'       => $this->isDraft(),
			'isPrivate'     => $this->isPrivate(),
			'isPublic'		=> $this->isPublic(),
		);

		return $article;
	}
}