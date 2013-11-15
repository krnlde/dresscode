<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Slider extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $class = '';

	/**
	 * @property
	 * @var boolean
	 */
	protected $autoplay = true;

	/**
	 * Delay between each slide.
	 *
	 * @property
	 * @var integer
	 * @pattern /^[0-9]{1,4}$/
	 */
	protected $speed = 5000;

	/**
	 * @property
	 * @var string
	 * @pattern /^none|slide|fade|reveal$/
	 */
	protected $transition = 'slide';

	/**
	 * @property
	 * @var integer
	 * @pattern /^[0-9]{1,4}$/
	 */
	protected $transitionSpeed = 1000;

	/**
	 * @property
	 * @var string
	 * @pattern /^up|down|left|right$/
	 */
	protected $direction = 'up';

	public function setup()
	{
		if (!$this->id)
		{
			$this->id = $this->generateId();
		}
		$this->Application->externalJavascript('/applications/common/assets/bootstrap/js/transition.js');
		$this->Application->externalJavascript('/applications/common/assets/bootstrap/js/carousel.js');
		// $this->Application->Javascript('$(".carousel").carousel();');
		// $this->Application->javascript
		// (	new StringAsset // initialize
		// 	('
		// 		$(function () {
		// 			$("#'.$this->id.'").carousel();
		// 		});
		// 	')
		// );
	}
}