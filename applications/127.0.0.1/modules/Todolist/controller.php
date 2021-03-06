<?php
namespace Dresscode\Controller;

class Todolist extends \Dresscode\Controller
{
	protected $dataprovider;

	public function setup()
	{
		$this->dataprovider = $this->findOne('\Dresscode\Controller\dataprovider');
		if (!$this->dataprovider)
		{
			$this->error(new \Dresscode\Exception('No dataprovider provided.'));
		}
		// $_SESSION = array();
		$dataprovider = $this->dataprovider;
		$form = $this->findOne('\Dresscode\Controller\Form');
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
			$this->node->appendChild($element = $this->dom->createElement('element'));
			$element->appendChild($this->dom->createTextNode($value)); // Important! It solves the "unterminated entity reference" bug from DomDocument::createElement().
		}
	}
}