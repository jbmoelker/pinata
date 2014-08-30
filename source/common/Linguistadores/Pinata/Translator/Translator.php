<?php

namespace Linguistadores\Pinata\Translator;

use \Curl\Curl;

class Translator {

	protected $baseUrl = 'http://es.linguistadores.com/workspace/scripts/translate.php';
	protected $sourceLang = 'en';
	protected $targetLang = 'en';

	/**
	 * [__construct description]
	 * @param string $sourceLang [description]
	 * @param string $targetLang [description]
	 */
	public function __construct($sourceLang, $targetLang)
	{
		$this->sourceLang = $sourceLang;
		$this->targetLang = $targetLang;
	}

	/**
	 * [setSourceLang description]
	 * @param string $sourceLang [description]
	 */
	public function setSourceLang($sourceLang)
	{
		$this->sourceLang = $sourceLang;
	}

	/**
	 * [setTargetLang description]
	 * @param string $targetLang [description]
	 */
	public function setTargetLang($targetLang)
	{
		$this->targetLang = $targetLang;
	}

	/**
	 * [translate description]
	 * @param  string $word [description]
	 * @return array        [description]
	 */
	public function translate($word)
	{
		$url = sprintf('%s?word=%s&sl=%s&tl=%s', $this->baseUrl, $word, $this->sourceLang, $this->targetLang);
		$curl = new Curl();
		$curl->get($url);
		$response = json_decode($curl->response);
		$translations = $response->translation[0]->translation;
		return $translations;
	}
}