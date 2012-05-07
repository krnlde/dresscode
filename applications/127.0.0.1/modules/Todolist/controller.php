<?php
namespace Mocovi\Controller;

class Todolist extends \Mocovi\Controller
{
	protected $dataprovider;

	public function setup()
	{
		$this->dataprovider = $this->findOne('\Mocovi\Controller\dataprovider');
		if (!$this->dataprovider)
		{
			$this->error(new \Mocovi\Exception('No dataprovider provided.'));
		}
		// $_SESSION = array();
		$dataprovider = $this->dataprovider;
		$form = $this->findOne('\Mocovi\Controller\Form');
		$form->context = $this;
		$form->on('success', function($event) use ($dataprovider) {
			if (isset($event->data['task']) && strlen($event->data['task']))
			{
				$dataprovider->add($event->data['task']);
			}
		});
	}

	public function get(array $params = array())
	{
		foreach ($this->dataprovider as $key => $value)
		{
			$this->Application->statusCode = 201; // Created
			$this->node->appendChild($this->dom->createElement('element', $value));
		}
	}

	public function post(array $params = array())
	{
		$this->get($params);
	}
}