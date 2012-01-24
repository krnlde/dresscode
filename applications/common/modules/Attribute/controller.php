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
		return $this->sourceNode->parentNode->setAttribute($this->name, null);
	}

	public function get(array $params = array())
	{
		$this->parent->setProperty($this->name, $this->node->nodeValue);
	}
}