<?php
namespace Dresscode\Controller;

class Error extends \Dresscode\Controller
{
	public function setup()
	{
		$this->Application->javascript(new \Assetic\Asset\FileAsset($this->Application->getCommonPath().'/assets/bootstrap/js/alert.js'));
	}
}