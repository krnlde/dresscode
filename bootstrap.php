<?php
/**
 * This constant defines the current unix time when the whole program starts.
 *
 * You can use it to measure computation time.
 */
define('STARTTIME', microtime(true));

require __DIR__.'/lib/vendor/Opl/src/Opl/Autoloader/GenericLoader.php';

use \Opl\Autoloader\GenericLoader;

$loader = new GenericLoader(__DIR__.'/lib/vendor/');
$loader->addNamespace('Mocovi');
$loader->addNamespace('Assetic', __DIR__.'/lib/vendor/Assetic/src');
$loader->addNamespace('CssMin', __DIR__.'/lib/vendor/CssMin/src');
$loader->register();

require 'options.php';

$application = new \Mocovi\Application
	( $applicationPool = new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'applications')
	, $options
	);