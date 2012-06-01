<?php
namespace Dresscode\Controller;

class Deleted extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $cite;

	/**
	 * @property
	 * @var string
	 */
	protected $datetime;

	protected $format = 'c';

	public function setup()
	{
		if ($this->datetime)
		{
			$this->datetime = date($this->format, strtotime($this->datetime));
		}
	}
}