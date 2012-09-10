<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

require_once(\Dresscode\Module::findController('Thumbnail'));

class Thumbnails extends \Dresscode\Controller
{
	/**
	 * Defines home many spans should be used for one Thumbnail.
	 *
	 * For Example: span = "4" means 3 Thumbnails each row based on a 12 column grid
	 * because 4 fits 3 times in 12 (12 / 4 = 3).
	 *
	 * @property
	 * @var integer
	 */
	protected $span = 4;
}