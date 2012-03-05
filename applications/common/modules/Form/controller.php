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
		$Application = $this->Application;
		$this->on('success', function ($event) use ($Application) { // @debug
			$event->preventDefault();
			print_r($event->relatedTarget);
		});

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
			catch (\Mocovi\Exception\Input $e) // Important! This prevents the Exception from bubbling up.
			{}
		}
	}

	protected function post(array $params = array())
	{
		if ($this->method === 'post' && count($this->Input->post) > 0)
		{
			$this->process();
		}
	}

	protected function process()
	{
		$invalid = false;
		$values = array();
		try
		{
			foreach ($this->inputs as $input)
			{
				$invalid |= $input->isValid();
				if ($input->isDataSent())
				{
					$input->validate();
					$values[$input->getProperty('name')] = $input->getProperty('value');
				}
			}
		}
		catch (\Mocovi\Exception\Input $e)
		{
			$this->trigger('error', $e);
			throw $e;
		}

		$event = $this->trigger('success', $values);
		if (!$event->isDefaultPrevented())
		{
			$this->Application->Response->Header->location($this->jumpTo);
		}
	}
}