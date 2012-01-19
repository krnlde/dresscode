<?php
namespace Mocovi\Controller;

class When extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $param;

	/**
	 * Space separated list of values
	 *
	 * @property
	 * @var string
	 */
	protected $in;

	protected function get(array $params = array())
	{
		if (!is_null($this->param) && isset($params[$this->param]) && in_array($params[$this->param], explode(' ', $this->in)))
		{
			parent::get($params);
		}
		else
		{
			$this->deleteNode();
		}
	}
}