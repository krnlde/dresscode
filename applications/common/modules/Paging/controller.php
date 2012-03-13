<?php
namespace Mocovi\Controller;

class Paging extends \Mocovi\Controller
{

	/**
	 * @property
	 * @var integer
	 */
	protected $default = 1;

	/**
	 * @var integer
	 */
	protected $count = 1;

	protected function before(array $params = array())
	{
		$count = &$this->count;
		$Input = \Mocovi\Input::getInstance();
		if (!is_null($Input->get('page')))
		{
			$page = $Input->get('page');
			if ($page < 1)
			{
				$page = 1;
			}
			elseif ($page > count($this->children))
			{
				$page = count($this->children);
			}
		}
		else
		{
			$page = ($this->default > 0 && $this->default <= count($this->children) ? $this->default : 1);
		}
		$this->on('launchChild', function ($event) use ($page, &$count) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			if ($page != $count)
			{
				$event->preventDefault();
			}
			$count++;
		});
	}
}