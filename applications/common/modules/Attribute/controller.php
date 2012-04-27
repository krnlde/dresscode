<?php
namespace Mocovi\Controller;

class Attribute extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $name;

	/**
	 * @property
	 * @var boolean
	 * @hidden
	 */
	protected $trim = true;

	public function get(array $params = array())
	{
		$value = $this->node->nodeValue;
		if ($this->trim)
		{
			$value = trim(preg_replace('/\s{2,}/', ' ', $value));
		}
		$this->parent->setProperty($this->name, $value); // @todo test this! This might strip WANTED whitespaces.
		$this->deleteNode();
	}
}