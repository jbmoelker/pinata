#!/usr/bin/env php
<?php

// Config
define(CURRENT_PATH, dirname(__FILE__));
define(ROOT_PATH, dirname(CURRENT_PATH));
define(OUTPUT_PATH, ROOT_PATH . "/source/content");
define(TEMPLATE_FILE, CURRENT_PATH . "/article-template.md");
define(FILE_EXTENSION, ".md");
define(PHP_STDIN, "php://stdin");

// Command line colors
define(COLOR_RED, "\033[0;31m");
define(COLOR_GREEN, "\033[0;32m");
define(COLOR_BLUE, "\033[0;34m");
define(COLOR_END, "\033[0m");

// Global variables
define(PREFIX_QUESTION, COLOR_GREEN . "[?]" . COLOR_END);
$userData = array();

// Banner
echo COLOR_BLUE . "[*] " . COLOR_END . "Generate Wiki Article Markdown file\n";

// Make sure the template can be found
function checkIfTemplateExists() {
	if (!file_exists(TEMPLATE_FILE)) {
		exit(COLOR_RED . "[-] " . COLOR_END . "Unabled to find template file: " . TEMPLATE_FILE . "\n");
	}
}

// Parse template
function parseTemplate($userData) {
	$lines = file(TEMPLATE_FILE);
	$parsedTemplate;

	foreach ($lines as $line_num => $line) {
		$line = preg_replace_callback("/\{\{\s?(\w+)\s?\}\}/", function($matches) use (&$userData) {
			return $userData[$matches[1]];
		}, $line);
		$parsedTemplate .= $line;
	}

	return $parsedTemplate;
}

// Slugify title for the filename
function getCleanFileName($title) {
	$fileName = preg_replace("/[^A-Za-z0-9-]+/", "-", strtolower($title));
	$fileName = rtrim($fileName, "-");

	return $fileName;
}

// Get the full path and check if the file name already exists
// If it already exists it adds the date [day-month-year]
function getPathAndFileName($title) {
	//$fullPath = dirname(__FILE__) . "/";
	$fullPath = OUTPUT_PATH . "/";
	$fileName = getCleanFileName($title);

	if (file_exists($fullPath . $fileName . FILE_EXTENSION)) {
		$date = getdate();
		$fileName .= "-" . $date["mday"] . "-" . $date["mon"] . "-" . $date["year"];
	}

	return $fullPath . $fileName . FILE_EXTENSION;
}

// Write the parsed template to disk
function writeToFile($parsedTemplate, $title) {
	$pathAndFileName = getPathAndFileName($title);

	$fh = fopen($pathAndFileName, 'a');
	$fw = fwrite($fh, $parsedTemplate);
	fclose($fh);

	if ($fw) {
		echo COLOR_GREEN . "[+] " . COLOR_END . "Created successfully: {$pathAndFileName}\n";
	}
	else {
		echo COLOR_RED . "[-] " . COLOR_END . "Failed to create {$pathAndFileName}\n";
	}
}

// Get the user input from the command line
function getCLIInput($name, $label, $required = false) {
	// Open command line stream
	$stdin = fopen(PHP_STDIN, "r");

	echo $label;

	// Read input from command line
	$cliInput = rtrim(fgets($stdin));

	// Close command line stream
	fclose($stdin);

	if ($required && empty($cliInput)) {
		echo COLOR_RED . "[-] " . COLOR_END . "This is a required field.\n";
		getCLIInput($name, $label, $required);
	}
	else {
		global $userData;
		$userData[$name] = $cliInput;
	}
}

// Script flow
checkIfTemplateExists(); // Terminates on error

// Get the user input and store it in $userData
getCLIInput("title", PREFIX_QUESTION . " Title*: ", true);
getCLIInput("description", PREFIX_QUESTION . " Description: ");
getCLIInput("authors", PREFIX_QUESTION . " Author(s) (comma separated)*: ", true);
getCLIInput("tags", PREFIX_QUESTION . " Tags (comma separated)*: ", true);
getCLIInput("image", PREFIX_QUESTION . " Image: ");

// Write parsed template to file
writeToFile(parseTemplate($userData), $userData["title"]);

// Terminate properly
exit(0);