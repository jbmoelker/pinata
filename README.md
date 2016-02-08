# Project Piñata

Project Piñata is an experiment created in 24h during the [Linguistadores' Hackfiesta](https://www.linguistadores.com/join-the-linguistadores-hackfiesta-hackathon/) by [@arendvw](https://twitter.com/xvronny), [@jbmoelker](https://twitter.com/jbmoelker) & [@xvronny](https://twitter.com/xvronny). The Piñata is a bookmarklet which when clicked creates a keyword index of the content of any web page and shows translations and related Metro articles based on those keywords. [Try out the demo](http://pinata.idlabs.nl/) by dragging the button saying Piñata to your bookmarkbar, visit a webpage and hit the bookmarklet. That's it.

## Getting started

### Clone repository

This project is hosted as a GIT repository. Clone it:

	$ git clone git@github.com:jbmoelker/pinata.git

Note: Unless stated otherwise, all shell commands in the rest of this Readme assume you run them from the project directory you just created by cloning the repository.

### Configure vhost

* Copy vhost file from `sample/pinata.vhost` to your vhost directory.
* Add `pinata.local.dev` to your `/etc/hosts` index file.

### Install dependencies

#### PHP dependencies

Project is written in PHP and therefore uses [composer](https://getcomposer.org/) for dependency management. Make sure you have composer installed (pro tip: [install globally](https://getcomposer.org/doc/00-intro.md#globally)):

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

This project is based on [De Voorhoede](http://www.voorhoede.nl)'s Wiki project and uses the common Voorhoede architecture with [Silex](http://silex.sensiolabs.org/) for routing & rendering and [Twig](http://twig.sensiolabs.org/) for HTML templating:

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
