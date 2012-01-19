<?php
/**
 * This file handles image manipulations like resizing and cropping.
 *
 * Caching is also enabled by default.
 *
 * @author Kai Dorschner <dorschner@enfire-studios.com>
 * @copyright Kai Dorschner 2011
 */
require 'library/Mocovi/Image.php';
require 'library/Mocovi/Input.php';

use   \Mocovi\Input
	, \Mocovi\Image
	;

$input		= Input::getInstance();
$filetype	= 'png';
$cacheTime	= 60; // seconds

$cacheKey =  $input->get('width')
			.'x'
			.$input->get('height')
			.'/'
			.implode
				( '_'
				, array
					( str_replace(array('\\', '/'), '_', $input->get('f'))
					, ($input->get('background') ? $input->get('background') : Image::BACKGROUNDCOLOR)
					, ($input->get('crop') ? 'cropped' : 'uncropped')
					, ($input->get('align') ? $input->get('align') : Image::ALIGN)
					)
				)
			.'.'
			.$filetype
			;
// header('Content-Type: image/'.$filetype);
if (apc_exists($cacheKey))
{
	die(apc_fetch($cacheKey));
}
else
{
	apc_store
		( $cacheKey
		, $value = Image::fromFile($input->get('f'))
			->crop
				( ($input->get('crop') ?: false)
				)
			->backgroundColor
				( ($input->get('background') ?: 'ffffff')
				)
			->align
				( ($input->get('align') ?: 'middle')
				)
			->resize
				( ($input->get('width') ? (int)$input->get('width') : null)
				, ($input->get('height') ? (int)$input->get('height') : null)
				)
			->$filetype()
		, $cacheTime
		);
	die($value);
}