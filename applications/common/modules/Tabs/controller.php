<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Tabs extends \Dresscode\Controller
{
	/**
	 *
	 * @property
	 * @var integer
	 */
	protected $foldAfter = 5;

	/**
	 * @property
	 * @var string
	 */
	protected $transition;

	public function setup()
	{
		$this->Application->externalJavascript('/applications/common/assets/bootstrap/js/tab.js');
		if ($this->transition)
		{
			$this->Application->externalJavascript('/applications/common/assets/bootstrap/js/transition.js');
		}
		if (count($this->children > $this->foldAfter))
		{
			$this->Application->externalJavascript('/applications/common/assets/bootstrap/js/dropdown.js');
		}
	}
}