<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

require_once(\Dresscode\Module::findController('Image'));

class Thumbnail extends \Dresscode\Controller\Image
{
	/**
	 * @property
	 * @var string
	 */
	protected $size = 'medium';

	/**
	 * @property
	 * @var boolean
	 */
	protected $crop = false;

	/**
	 * @var array of string
	 */
	protected $sizes = array('small', 'medium', 'large');

	/**
	 * @var \Assetic\Asset\StringAsset
	 */
	protected static $initializeScript;

	public function setup()
	{
		if (!self::$initializeScript)
		{
			self::$initializeScript = new StringAsset
			(
				'$(function () {
					$("a[rel]").click(function (event) {
						event.preventDefault();
					}).fancybox({
						openEffect	: "fade",
						closeEffect	: "none",
						prevEffect	: "fade",
						nextEffect	: "fade",
						helpers		: {
							title : {
								type : "over"
							}
						}
					});
				});'
			);
		}
		$this->Application->externalStylesheet('/applications/common/modules/Thumbnail/assets/fancyBox/source/jquery.fancybox.css');
		$this->Application->externalJavascript('/applications/common/modules/Thumbnail/assets/fancyBox/source/jquery.fancybox.js');
		$this->Application->javascript(self::$initializeScript);
		if (!in_array($this->size, $this->sizes))
		{
			$this->size = $this->sizes[ceil(count($this->sizes) * 0.5) - 1]; // median
		}
		parent::setup();
	}
}