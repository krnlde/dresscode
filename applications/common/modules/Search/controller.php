<?php
namespace Dresscode\Controller;

class Search extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $parameter = 'q';

	/**
	 * @property
	 * @var string
	 */
	protected $class = 'search';


	public function get(array $params = array())
	{
		$Input = \Dresscode\Input::getInstance();
		if (isset($Input->get[$this->parameter]))
		{
			$this->addChild($text = new \Dresscode\Controller\Plain(null, $this->Application)); // created on the fly
			$text->launch(__FUNCTION__);
			$text->setText($Input->get[$this->parameter]);
		}
	}
}