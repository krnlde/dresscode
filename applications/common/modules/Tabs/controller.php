<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Tabs extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var integer
	 */
	protected $maximum = 5;

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
		if (count($this->children > $this->maximum))
		{
			$this->Application->javascript(new FileAsset('applications/common/assets/bootstrap/js/bootstrap-dropdown.js'));
		}
	}
}