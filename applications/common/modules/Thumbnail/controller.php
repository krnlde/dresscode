<?php
namespace Mocovi\Controller;
class_exists('\\Mocovi\\Controller\\Image', false) or require(\Mocovi\Module::findController('Image'));

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
		if (!in_array($this->size, $this->sizes))
		{
			$this->size = $this->sizes[ceil(count($this->sizes) * 0.5) - 1]; // median
		}
	}
}