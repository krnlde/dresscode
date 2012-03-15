<?php
namespace Mocovi\Controller;

class Attribute extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $name;

	public function get(array $params = array())
	{
		$this->parent->setProperty($this->name, $this->node->nodeValue);
	}
}