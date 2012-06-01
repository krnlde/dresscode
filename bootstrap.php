<?php
/**
 * This constant defines the current unix time when the whole program starts.
 *
 * You can use it to measure computation time.
 */
define('STARTTIME', microtime(true));

require __DIR__.'/lib/vendor/Opl/src/Opl/Autoloader/UniversalLoader.php';

use \Opl\Autoloader\UniversalLoader;

$loader = new UniversalLoader(__DIR__.'/lib/vendor/');
// $loader->addNamespace('Dresscode\Controller\\', __DIR__.'/applications/common/modules'); // @todo
$loader->addNamespace('Dresscode');
$loader->addNamespace('Assetic', __DIR__.'/lib/vendor/Assetic/src');
$loader->addNamespace('Symfony', __DIR__.'/lib/vendor/Symfony/src');
$loader->register();

require 'options.php';

$application = new \Dresscode\Application
	( $applicationPool = new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'applications')
	, $options
	);