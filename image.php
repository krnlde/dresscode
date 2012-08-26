<?php

require __DIR__.'/lib/vendor/Opl/src/Opl/Autoloader/GenericLoader.php';

$loader = new \Opl\Autoloader\GenericLoader(__DIR__.'/lib/vendor/');
$loader->addNamespace('Imagine', __DIR__.'/lib/vendor/Imagine/lib');
$loader->register();

$source	= $_GET['source'];
if ($source[0] === '/')
{
	$source = $_SERVER['DOCUMENT_ROOT'].$source;
}

if (!file_exists($source)) {
	die('Not found');
}

$extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
$cachedir = __DIR__.'/cache/';
$key = md5(serialize($_GET));
$cachesource = $cachedir.$key.'.'.$extension;

header('Content-Type: image/'.$extension);
// header('content-disposition....');
// header('Last-Modified');

if (file_exists($cachesource)) {
	if (filemtime($source) <= filemtime($cachesource)) {
		die(file_get_contents($cachesource));
	}
}

if (isset($_GET['width']))
{
	$size = new Imagine\Image\Box( (int)$_GET['width'], (int)(isset($_GET['height']) ? $_GET['height'] : $_GET['width']) );
}
else
{
	$size = new Imagine\Image\Box(697, 697); // max image size in iPad responsive design
}

if (isset($_GET['crop']) && $_GET['crop'])
{
	$mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND; // Crop
}
else
{
	$mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET; // Size to fit
}
$imagine = new \Imagine\Gd\Imagine();
echo $imagine->open($source)
	->thumbnail($size, $mode)
	->save($cachesource);