<?php
require __DIR__.'/lib/vendor/Opl/src/Opl/Autoloader/GenericLoader.php';

ini_set('memory_limit', '256M');

$loader = new \Opl\Autoloader\GenericLoader(__DIR__.'/lib/vendor/');
$loader->addNamespace('Dresscode', __DIR__.'/lib/vendor');
$loader->addNamespace('Imagine', __DIR__.'/lib/vendor/Imagine/lib');
$loader->register();

$source	= $_GET['source'];
if ($source[0] === '/') {
	$source = $_SERVER['DOCUMENT_ROOT'].$source;
}

if (!file_exists($source)) {
	die('Not found');
}

$mtime = filemtime($source);
$extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
$cachedir = __DIR__.'/cache/';
$key = md5(serialize($_GET));
$cachesource = $cachedir.$key.'.'.$extension;

$Request = \Dresscode\Request::getInstance();
$Response = \Dresscode\Response::getInstance();

if ($Request->if_modified_since && strtotime($mtime) <= strtotime($Request->if_modified_since)) {
	$Response->end(null, 304); // Not modified
}

$imagine = null;
if (extension_loaded('imagick')) {
  header('X-Rendered-With: Imagick');
  $imagine = new \Imagine\Imagick\Imagine();
} elseif (extension_loaded('gmagick')) {
  header('X-Rendered-With: Gmagick');
  $imagine = new \Imagine\Gmagick\Imagine();
} elseif(extension_loaded('gd')) {
  header('X-Rendered-With: GD');
  $imagine = new \Imagine\Gd\Imagine();
} else {
  throw new Exception('No image library found.');
}

$image = $imagine->open($source);

header('Date: ' . date('r'));
header('Last-Modified: ' . date('r', $mtime));
header('Content-Type: image/'.($extension == 'jpg' ? 'jpeg' : $extension));
header('Content-Disposition: inline; filename="' . basename($source) . '"');

if (file_exists($cachesource)) {
	if (filemtime($source) <= filemtime($cachesource)) {
		die(file_get_contents($cachesource));
	}
}

if (isset($_GET['width'])) {
	$size = new Imagine\Image\Box(
		(int)$_GET['width'],
		(int)(isset($_GET['height']) ? $_GET['height'] : $_GET['width'])
	);
} else {
	// $size = new Imagine\Image\Box(709, 709); // max image size in iPad responsive design
	$size = new Imagine\Image\Box(370, 370); // max image size in iPad responsive design
}

if (isset($_GET['crop']) && $_GET['crop']) {
	$mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND; // Crop
} else {
	$mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET; // Size to fit
}

echo $image
	->thumbnail($size, $mode)
	->interlace(Imagine\Image\ImageInterface::INTERLACE_PLANE)
	->save($cachesource, array('quality' => 75));

unset($image);
exit;