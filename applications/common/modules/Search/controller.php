<?php
namespace Mocovi\Controller;

class Search extends \Mocovi\Controller
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
		$Input = \Mocovi\Input::getInstance();
		if (isset($Input->get[$this->parameter]))
		{
			$this->addChild($text = new \Mocovi\Controller\Plain(null, $this->Application)); // created on the fly
			$text->launch(__FUNCTION__);
			$text->setText($Input->get[$this->parameter]);
		}
	}
}