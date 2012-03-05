<?php
namespace Mocovi\Controller;

\Mocovi\Module::requireController('Element');

class Protect extends \Mocovi\Controller\Element
{
	/**
	 * @property
	 * @var string
	 */
	protected $require;

	public function before(array $params = array())
	{
		$require = $this->require;
		$this->on('launchChild', function($event) use ($require) {
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