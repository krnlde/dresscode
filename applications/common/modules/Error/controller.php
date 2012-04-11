<?php
namespace Mocovi\Controller;

class Error extends \Mocovi\Controller
{
	public function setup()
	{
		$this->Application->javascript(new \Assetic\Asset\FileAsset('applications/common/assets/bootstrap/js/bootstrap-alert.js'));
	}
}