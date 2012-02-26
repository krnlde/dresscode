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
		$this->setText(date($this->format, $time));
	}

	// @todo recognize time zone and time formatting (de, en, etc.)
}