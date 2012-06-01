<?php
namespace Dresscode\Controller;

use \Dresscode\Translator;

\Dresscode\Module::requireController('Time');

class Duration extends \Dresscode\Controller\Time
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

	public function get(array $params = array())
	{
		$this->format	= $this->format[0]; // str2char
		$from			= new \DateTime($this->from);
		$to				= new \DateTime($this->to);
		if (!in_array($this->format, $this->formats))
		{
			$this->format = $this->formats[0];
		}
		$this->node->appendChild($this->dom->createTextNode($from->diff($to)->format('%'.$this->format)));
	}
}