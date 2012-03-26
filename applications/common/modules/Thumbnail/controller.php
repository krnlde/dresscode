<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

require_once(\Mocovi\Module::findController('Image'));

class Thumbnail extends \Mocovi\Controller\Image
{
	/**
	 * @property
	 * @var string
	 */
	protected $size = 'medium';

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
				'$("a[rel]").click(function (event) {
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
				});'
			);
		}
		$this->Application->stylesheet(new FileAsset('applications/common/modules/Thumbnail/assets/jquery-fancybox/source/jquery.fancybox.css'));
		$this->Application->javascripts
			( array
				( new FileAsset('applications/common/modules/Thumbnail/assets/jquery-fancybox/source/jquery.fancybox.pack.js')
				, self::$initializeScript
				)
			);
		if (!in_array($this->size, $this->sizes))
		{
			$this->size = $this->sizes[ceil(count($this->sizes) * 0.5) - 1]; // median
		}
		parent::setup();
	}
}