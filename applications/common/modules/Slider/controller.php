<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Slider extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $class = 'slider';

	/**
	 * @property
	 * @var boolean
	 */
	protected $autoplay = true;

	/**
	 * @property
	 * @var integer
	 * @pattern /[0-9]{1,5}/
	 */
	protected $rotationSpeed = 5000;

	/**
	 * @property
	 * @var string
	 * @pattern /up|down|left|right/
	 */
	protected $direction = 'up'; // up, down, left, right

	protected function before(array $params = array())
	{
		$this->Application->stylesheet(new FileAsset('applications/common/modules/Slider/assets/css/slider.css'));
		$this->Application->javascripts
		(	array
			(	new FileAsset('applications/common/assets/js/jquery.min.js') // or 'http://code.jquery.com/jquery.min.js'
			,	new FileAsset('applications/common/modules/Slider/assets/js/jquery-revolver/jquery.revolver.min.js')
			,	new StringAsset // initialize
				('
					$(function() {
						var $slider = $(".slider");
						$slider.find(".slide:first-child").css({display: "block"});
						var revolver = $slider.revolver(
							{ autoPlay:			'.($this->autoplay ? 'true' : 'false').'
							, rotationSpeed:	'.$this->rotationSpeed.'
							, transition:
								{ direction:	"'.$this->direction.'"
								, easing:		"swing"
								, speed:		1000
								, type:			"slide"
								}
							}
						).data("revolver");
					});
				')
			)
		);
	}
}