<?php
namespace Dresscode\Controller;

\Dresscode\Module::requireController('Element');

class Protect extends \Dresscode\Controller\Element
{
	/**
	 * @property
	 * @var string
	 */
	protected $require;

	public function setup()
	{
		$require = $this->require;
		$this->on('launchChild', function($event) use ($require) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			if (strtolower($require) !== 'valid-user')
			{
				$event->preventDefault();
			}
		});
	}

	protected function createNode()
	{
		return $this->dom->createElement('element');
	}
}