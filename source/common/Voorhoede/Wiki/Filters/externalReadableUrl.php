<?php

/**
 * externalReadableUrl
 *
 * Returns a readable outgoing URL
 */
$filterExternalReadableUrl = new Twig_SimpleFilter('externalReadableUrl', function ($string) {
	$protocolPattern = '/^(http|https):\/\/|^(\/\/)/';

	$readableUrl = preg_replace($protocolPattern, '', $string);
	$readableUrl = rtrim($readableUrl, '/');

	return $readableUrl;
});