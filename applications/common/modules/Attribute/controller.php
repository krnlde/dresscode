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
		$this->parent->setProperty($this->name, trim(preg_replace('/\s{2,}/', ' ', $this->node->nodeValue))); // @todo test this! This might strip WANTED whitespaces.
		$this->deleteNode();
	}
}