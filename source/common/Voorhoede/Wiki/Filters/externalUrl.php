<?php

/**
 * externalUrl
 *
 * Returns an outgoing URL when it starts with `www.`
 */
$filterExternalUrl = new Twig_SimpleFilter('externalUrl', function ($string) {
	$wwwPattern = '/^(www\.)/';

	if (preg_match($wwwPattern, $string)) {
		$string = '//' . $string;
	}

	return $string;
});