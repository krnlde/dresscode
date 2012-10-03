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
		$this->Application->javascript(new FileAsset('applications/common/assets/bootstrap/js/bootstrap-tab.js'));
		if ($this->transition)
		{
			$this->Application->javascript(new FileAsset('applications/common/assets/bootstrap/js/bootstrap-transition.js'));
		}
		if (count($this->children > $this->foldAfter))
		{
			$this->Application->javascript(new FileAsset('applications/common/assets/bootstrap/js/bootstrap-dropdown.js'));
		}
	}
}