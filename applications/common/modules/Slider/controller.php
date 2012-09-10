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
	protected $class = 'slider';

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
		$this->Application->stylesheet(new FileAsset('applications/common/modules/Slider/assets/css/slider.css'));
		$this->Application->javascripts
		(	array
			(	new FileAsset('applications/common/modules/Slider/assets/js/jquery-revolver/jquery.revolver.min.js')
			,	new StringAsset // initialize
				('
					$(function() {
						var $slider = $(".'.$this->class.'");
						$slider.find(".slide:first-child").css({display: "block"});
						var revolver = $slider.revolver(
							{ autoPlay:			'.($this->autoplay ? 'true' : 'false').'
							, rotationSpeed:	'.$this->speed.'
							, transition:
								{ direction:	"'.$this->direction.'"
								, easing:		"swing"
								, speed:		'.$this->transitionSpeed.'
								, type:			"'.$this->transition.'"
								}
							}
						).data("revolver");

						$slider.hover(function () {
							revolver.pause();
						},
						function () {
							revolver.play();
						});
					});
				')
			)
		);
	}
}