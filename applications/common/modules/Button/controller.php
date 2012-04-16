<?php
namespace Mocovi\Controller;

use \Assetic\Asset\StringAsset;

class Button extends \Mocovi\Controller
{
	/**
	 * Type of the input.
	 *
	 * @property
	 * @var string
	 */
	protected $type = 'submit';

	/**
	 * Name of the input.
	 *
	 * @property
	 * @var string
	 */
	protected $name;

	/**
	 * @property
	 * @hidden
	 * @var string
	 */
	protected $onclick;

	protected static $initialize;

	public function get(array $params = array())
	{
		if ($this->onclick)
		{
			if (!$this->id)
			{
				$this->id = $this->generateId();
			}

			if (is_null(self::$initialize))
			{
				self::$initialize = new StringAsset
				(
					'
					var $basepath	= "'.\Mocovi\Application::basePath().'";
					var $name		= "'.$this->getName().'";
					'
				);
			}

			$this->Application->javascripts( array
			(	self::$initialize // include only one time
			,	new StringAsset
				(
					'
					$("#'.$this->id.'").click(function (event) {
						var $id = "'.$this->id.'";
						'.$this->onclick.'
					});
					'
				)
			));
		}
	}
}