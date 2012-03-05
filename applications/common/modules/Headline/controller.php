<?php
namespace Mocovi\Controller;

class Headline extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var integer
	 */
	protected $priority = 1;

	protected function get(array $params = array())
	{
		parent::get($params);

		if ($this->priority < 1)
		{
			$this->priority = 1;
		}
		elseif ($this->priority > 6)
		{
			$this->priority = 6;
		}

		if (!$this->id) // Set an ID with maxlength 32, if none is set.
		{
			$this->id = substr(urlencode(trim($this->node->nodeValue)), 0, 32);
		}
	}
}