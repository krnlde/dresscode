<?php
namespace Mocovi\Controller;

use Assetic\Asset;

class Slider extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $class = 'slider';

	protected function before(array $params = array())
	{
		$this->Application->stylesheets
		(	array
			(	'applications/common/modules/Slider/assets/css/slider.css'
			)
		);
		$this->Application->javascripts
		(	array
			(	'applications/common/assets/js/jquery.min.js' // or 'http://code.jquery.com/jquery.min.js'
			,	'applications/common/modules/Slider/assets/js/jquery.revolver.min.js'
			,	'applications/common/modules/Slider/assets/js/slider.js'
			)
		);
		parent::before($params);
	}
}