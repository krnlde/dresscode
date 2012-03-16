<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;

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

	protected function before(array $params = array())
	{
		parent::before($params);
		$this->Application->stylesheet(new FileAsset('applications/common/modules/Thumbnail/assets/jquery-fancybox/source/jquery.fancybox.css'));
		$this->Application->javascript(new FileAsset('applications/common/modules/Thumbnail/assets/jquery-fancybox/source/jquery.fancybox.pack.js'));
		$this->Application->javascript(new FileAsset('applications/common/modules/Thumbnail/assets/js/initialize.js'));
		if (!in_array($this->size, $this->sizes))
		{
			$this->size = $this->sizes[ceil(count($this->sizes) * 0.5) - 1]; // median
		}
	}
}