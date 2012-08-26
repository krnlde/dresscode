<?php
namespace Dresscode\Controller;

class Image extends \Dresscode\Controller
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

	public function get(array $params = array())
	{
		parent::get($params);
		if ($this->source[0] === '/') // Root Directory
		{
			$dirname = dirname($_SERVER['SCRIPT_NAME']);
			if ($dirname !== '/')
			{
				$this->source = $dirname.$this->source;
			}
		}
		if ($this->source[0].$this->source[1] === '~/') // Application Home Directory
		{
			$dirname = $this->Application->basePath().'/applications/'.$this->Application->getName();
			$this->source = $dirname.substr($this->source, 1);
		}
		if (empty($this->description))
		{
			$this->description = $this->source;
		}
	}
}