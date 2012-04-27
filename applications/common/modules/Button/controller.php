<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
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

	public function setup()
	{
		$this->Application->javascript(new FileAsset(__DIR__.'/assets/js/spin.min.js')); // @todo temporarily
		$this->Application->javascript(new FileAsset(__DIR__.'/assets/js/jquery.spin.js')); // @todo temporarily
	}

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
			$self			= $this;
			$Application	= $this->Application;
			$onclick		= $this->onclick;
			$Application->javascript(self::$initialize);
			$this->closest('Root')->on('ready', function ($event) use ($self, $Application, $onclick) { // @todo "use ($self)"" is obsolote in PHP > 5.4
				$Application->javascript
				(	new StringAsset
					(
						'
						$("#'.$self->getProperty('id').'").click(function (event) {
							var $this	= $(this);
							var $id		= "'.$self->getProperty('id').'";
							var $xpath	= "'.$self->getXPath().'";

							'.$onclick.'
						});
						'
					)
				);
			});
		}
	}
}