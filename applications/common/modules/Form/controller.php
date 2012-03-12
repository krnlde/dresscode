<?php
namespace Mocovi\Controller;

class Form extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $jumpTo;

	/**
	 * @property
	 * @var string
	 */
	protected $method = 'post';

	/**
	 * @var array
	 */
	protected $methods = array
	( 'get'
	, 'post'
	);

	/**
	 * @var \Mocovi\Input
	 */
	protected $Input;

	/**
	 * @var array of \Mocovi\Controller\Input
	 */
	protected $inputs;


	protected function before(array $params = array())
	{
		if (strlen($this->jumpTo) > 0 && $this->jumpTo[0] === '/')
		{
			$this->jumpTo = \Mocovi\Application::basePath().$this->jumpTo;
		}
		$this->Input = \Mocovi\Input::getInstance();
		$this->inputs = $this->find('Input');
		if (!in_array($this->method, $this->methods))
		{
			$this->method = $this->methods[count($this->methods) - 1];
		}
	}

	protected function get(array $params = array())
	{
		if ($this->method === 'get' && count($this->Input->get) > 0)
		{
			try
			{
				$this->process();
			}
			catch (\Mocovi\Exception\Input $e)
			{} // Important! This prevents the Exception from bubbling up.
		}
	}

	protected function post(array $params = array())
	{
		if ($this->method === 'post' && count($this->Input->post) > 0)
		{
			$this->process();
		}
	}

	/**
	 * Processes the input values and fires the "success" event on success.
	 *
	 * If execute $event->preventDefault(); on the success event, the jumpTo
	 * header redirect will be ignored.
	 *
	 * If one input type validation check fails an "error" event will be triggered.
	 *
	 * @triggers success
	 * @triggers error
	 */
	protected function process()
	{
		$values = array();
		try
		{
			foreach ($this->inputs as $input)
			{
				if ($input->isDataSent())
				{
					if($input->isValid())
					{
						$values[$input->getProperty('name')] = $input->getProperty('value'); // @todo array values like &equipment[]=23
					}
					else
					{
						throw $input->exception;
					}
				}
			}
		}
		catch (\Mocovi\Exception\Input $e)
		{
			if (!$this->trigger('error', $e)->isDefaultPrevented())
			{
				throw $e;
			}
		}
		/*
			The success event will only be triggered when the size of the
			inputs inside this form and the count of the received values are matching.
			Otherwise it's assumed that this is not the correct form.
		*/
		if (count($values) === $this->inputs->length)
		{
			if (!$this->trigger('success', /*relatedTarget*/ null, /*data*/ $values)->isDefaultPrevented())
			{
				$this->Application->Response->Header->location($this->jumpTo);
			}
		}
	}
}