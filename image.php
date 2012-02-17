<?php

require __DIR__.'/lib/vendor/Opl/src/Opl/Autoloader/GenericLoader.php';

use \Opl\Autoloader\GenericLoader;

$loader = new GenericLoader(__DIR__.'/lib/vendor/');
$loader->addNamespace('Imagine', __DIR__.'/lib/vendor/Imagine/lib');
$loader->register();

$source	= $_GET['source'];
if ($source[0] === '/')
{
	$source = $_SERVER['DOCUMENT_ROOT'].$source;
}

$size		= new Imagine\Image\Box(160, 100);
$mode		= Imagine\Image\ImageInterface::THUMBNAIL_INSET;
$imagine	= new \Imagine\Gd\Imagine();
header('Content-Type: image/png');
echo $imagine->open($source)
	->thumbnail($size, $mode);