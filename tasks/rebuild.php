#!/usr/bin/env php
<?php

// Remove cache folders
echo exec("rm -rf cache/content/");
echo exec("rm -rf cache/profiler/");
echo exec("rm -rf cache/search/");
echo exec("rm -rf cache/twig/");

echo "\033[0;32m"; // Start coloring (green)

echo "Cache folders removed.\n";

// Generate search index
echo exec("php tasks/console.php wiki:index");

echo "\033[0m\n"; // End coloring

exit(0);