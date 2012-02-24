<?php
namespace Mocovi\Controller;

use \Mocovi\Translator;

\Mocovi\Module::requireController('Date');

class Duration extends \Mocovi\Controller\Date
{

	protected $formats = array
	( 'y' // years
	, 'm' // months
	, 'd' // days
	, 'h' // hours
	, 'i' // minutes
	, 's' // seconds
	, 'a' // absolute days - @todo: bug on windows, always returns 6015
	);

	/**
	 * @property
	 * @var string
	 */
	protected $from = 'now';

	/**
	 * @property
	 * @var string
	 */
	protected $to = 'now';

	protected function get(array $params = array())
	{
		$this->format	= $this->format[0]; // str2char
		$from			= new \DateTime($this->from);
		$to				= new \DateTime($this->to);
		if (!in_array($this->format, $this->formats))
		{
			$this->format = $this->formats[0];
		}
		$this->setText($from->diff($to)->format('%'.$this->format));
	}
}