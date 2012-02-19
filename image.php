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
if (isset($_GET['size']) && $_GET['size'] === 'small')
{
	$size		= new Imagine\Image\Box(50, 100);
}
elseif (isset($_GET['size']) && $_GET['size'] === 'large')
{
	$size		= new Imagine\Image\Box(200, 400);
}
else
{
	$size		= new Imagine\Image\Box(100, 200);
}
// $mode		= Imagine\Image\ImageInterface::THUMBNAIL_INSET;
$mode		= Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
$imagine	= new \Imagine\Gd\Imagine();
header('Content-Type: image/png');
echo $imagine->open($source)
	->thumbnail($size, $mode);