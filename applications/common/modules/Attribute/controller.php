<?php
namespace Mocovi\Controller;

class Attribute extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $name;

	protected function createNode()
	{
		return $this->parentNode->setAttribute($this->name, null); // will be filled later
	}
}