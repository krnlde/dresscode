<?php
namespace Dresscode\Controller;

class When extends \Dresscode\Controller
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

	public function get(array $params = array())
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