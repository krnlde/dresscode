<?php
/**
 * This constant defines the current unix time when the whole program starts.
 *
 * You can use it to measure computation time.
 */
define('STARTTIME', microtime(true));

require __DIR__.'/lib/vendor/Opl/Autoloader/GenericLoader.php';

use \Opl\Autoloader\GenericLoader;

$loader = new GenericLoader('lib/vendor/');
$loader->addNamespace('Mocovi');
$loader->addNamespace('Assetic', 'lib/vendor/Assetic/src');
$loader->register();

require 'options.php';

$applicationPool	= new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'applications');

$application = new \Mocovi\Application($applicationPool, $options);