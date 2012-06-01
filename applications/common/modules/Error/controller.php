<?php
namespace Dresscode\Controller;

class Error extends \Dresscode\Controller
{
	public function setup()
	{
		$this->Application->javascript(new \Assetic\Asset\FileAsset('applications/common/assets/bootstrap/js/bootstrap-alert.js'));
	}
}