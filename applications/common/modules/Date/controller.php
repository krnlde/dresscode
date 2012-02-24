<?php
namespace Mocovi\Controller;

\Mocovi\Module::requireController('Plain');

class Date extends \Mocovi\Controller\Plain
{
	/**
	 * @property
	 * @var string
	 */
	protected $format = 'r';

	protected function get(array $params = array())
	{
		if ($this->sourceNode->nodeValue)
		{
			$time = strtotime($this->sourceNode->nodeValue);
		}
		else
		{
			$time = time(); // now
		}

		$this->setText($this->formatTime($time));
	}


	/**
	 * Formats a time into the predefined conversion format.
	 *
	 * All formatting strings of the function date(); can be used.
	 *
	 * @param string $time Contains the unix time which will be formatted.
	 * @return integer Formatted time value.
	 */
	protected function formatTime($unixtime)
	{
		return date($this->format, $unixtime);
	}

	// @todo recognize time zone and time formatting (de, en, etc.)
}