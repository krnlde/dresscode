<?php
namespace Mocovi\Controller;

class Listing extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $type = 'unordered';

	/**
	 * @property
	 * @var int
	 */
	protected $maximum;

	protected function get(array $params = array())
	{
		if (!is_null($this->maximum))
		{
			while (($count = count($this->children)) > $this->maximum && $count > 0)
			{
				$lastChild = $this->children[$count - 1];
				$this->node->removeChild($lastChild->getNode());
				unset($this->children[$count - 1]);
			}
		}
		parent::get($params); // execute children after removing backlog
	}
}