<?php
namespace Mocovi\Controller;

class Image extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $source;

	/**
	 * Groups images for example for lightbox slides.
	 *
	 * @property
	 * @var string
	 */
	protected $group;

	/**
	 * @property
	 * @var string
	 */
	protected $description = '';

	/**
	 * @property
	 * @var string
	 */
	protected $orientation = 'middle';

	/**
	 * @property
	 * @var boolean
	 */
	protected $crop = true;

	protected function get(array $params = array())
	{
		parent::get($params);
		if ($this->source[0] === '/')
		{
			$this->source = dirname($_SERVER['SCRIPT_NAME']).$this->source;
		}
		if(empty($this->description))
		{
			$this->description = $this->source;
		}
	}
}