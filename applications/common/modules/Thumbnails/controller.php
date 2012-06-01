<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

require_once(\Dresscode\Module::findController('Thumbnail'));

class Thumbnails extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var boolean
	 */
	protected $more = true;
}