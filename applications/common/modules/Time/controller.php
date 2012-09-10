<?php
namespace Dresscode\Controller;

class Time extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $format = 'r';
	/**
	 * @property
	 * @var string
	 */
	protected $datetime;

	public function setup()
	{
		// $this->on('launchChild', function ($event) {
		// 	$event->preventDefault(); // block all children
		// });
	}

	public function get(array $params = array())
	{
		if ($this->datetime)
		{
			$time = strtotime($this->datetime);
		}
		else
		{
			$time = time(); // now
		}

		$this->node->setAttribute('datetime', date('Y-m-d', $time));
		$this->node->appendChild($this->dom->createTextNode(date($this->format, $time)));
	}

	// @todo recognize time zone and time formatting (de, en, etc.)
}