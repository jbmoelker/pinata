<?php

/**
 * internalReadableUrl
 *
 * Returns a readable internal URL
 */
$filterInternalReadableUrl = new Twig_SimpleFilter('internalReadableUrl', function ($string) {
	$string = str_replace('-', ' ', $string);
	$string = ucfirst($string);

	return $string;
});