# Voorhoede Wiki

Voorhoede Wiki is [De Voorhoede](http://www.voorhoede.nl)'s online documentation environment. It's a collection of articles in [Markdown](http://daringfireball.net/projects/markdown/) format, enhanced with metadata in [YAML](http://en.wikipedia.org/wiki/YAML) format. The [Silex](http://silex.sensiolabs.org/) micro framework is used to parse, index and render the articles are rendered as HTML.

## Getting started

### Clone repository

This project is hosted as a Voorhoede GIT repository. Clone it:

	$ git clone git@bitbucket.org:voorhoede/voorhoede-wiki.git

Note: Unless stated otherwise, all shell commands in the rest of this Readme assume you run them from the project directory you just created by cloning the repository.

### Configure vhost

* Copy vhost file from `sample/voorhoede-wiki.vhost` to your vhost directory.
* Add `local.voorhoede-wiki.voorhoede.nl` to your `/etc/hosts` index file.

### Install dependencies

#### PHP dependencies

Voorhoede Wiki is written in PHP and therefore uses [composer](https://getcomposer.org/) for dependency management. Make sure you have composer installed (pro tip: [install globally](https://getcomposer.org/doc/00-intro.md#globally)):

	$ curl -sS https://getcomposer.org/installer | php

From the project root directory install dependencies using composer:

	$ php composer.phar install

Or if you have already installed the dependencies but they are out of date:

	$ php composer.phar update

And read [Composer Namespaces in 5 Minutes](https://jtreminio.com/2012/10/composer-namespaces-in-5-minutes/) if you want to know more about autoloading and namespacing in PHP.

### Removing cache and generate new search index

From the root of the project run the following PHP task to remove the cache folders and rebuild the search index:

	$ php tasks/rebuild.php

### Updating the search index

From the root of the project run the following PHP task to update the search index:

	$ php tasks/console.php wiki:index

## Architecture

This project uses the common Voorhoede architecture with [Silex](http://silex.sensiolabs.org/) for routing & rendering and [Twig](http://twig.sensiolabs.org/) for HTML templating:

	cache/							<-- template and search index cache
	config/							<-- production and development configuration files
	source/
		assets/
			fonts/
			images/
			scripts/
			scss/
			styles/					<-- base.css & main.css compiled from scss
		content/					<-- wiki documents in markdown format
		modules/
			components/
			views/
		app.php
	logs/
	tasks/							<-- PHP tasks
	vendor/
	web/							<-- index controller and published assets

### Templates

This project uses [Twig](http://twig.sensiolabs.org/) for HTML templating. Twig is a powerful templating engine written in PHP. Twig allows us to use variables, expressions & filters and supports including & extending other templates.
The syntax of Twig is very similar to [Jinja](http://jinja.pocoo.org/) (Python), [Nunjucks](http://jlongster.github.io/nunjucks/) (JavaScript) and [Liquid](http://liquidmarkup.org/) (Ruby).

[IDEs with Twig support](http://twig.sensiolabs.org/doc/templates.html#ides-integration) for syntax highlighting and auto-completion.
WebStorm is not listed but you can install the plugin manually: Download [Twig Jetbrains plugin](http://plugins.jetbrains.com/plugin/7303?pr=phpStorm), open your WebStorm settings and go to plugins. Select 'Install plugin from disk...' and select the downloaded zip file.
To associate the template files with Twig, select 'File Types' in your project settings, select 'Twig' and add `*.html`, overwriting default wildcard association.

## Articles

You can read the articles inside this Wiki online or in any Markdown viewer. To author an article you should read '[Writing Wiki Articles](https://bitbucket.org/voorhoede/voorhoede-wiki/src/master/source/content/using-markdown.md)'.

### Generate article file

To generate a Markdown article with the meta data used on this Wiki you can run the following command:

	$ php tasks/article.php

This script will ask you a few questions so it can generate the `.md` file in the `source/content/` folder with the meta data and slugified file name.
