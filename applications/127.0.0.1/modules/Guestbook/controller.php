<?php
namespace Dresscode\Controller;

class Guestbook extends \Dresscode\Controller
{
	public function setup()
	{
		if (!$this->class)
		{
			$this->class = strtolower($this->getName());
		}
		$self = $this;
		$this->find('Form')->on('success', function ($event) use ($self) { // @todo you can use $this directly in PHP 5.4
			$event->preventDefault(); // @debug
			$data = (object)$event->data;
			$self->saveElement($data->name, $data->email, $data->text);
		});
	}

	public function saveElement($name, $email, $text)
	{
		// Do Database stuff here.
		$this->node->appendChild($wrapper = $this->dom->createElement('section'));
		$wrapper->setAttribute('class', 'error');
		$wrapper->appendChild($headline = $this->dom->createElement('headline', $name));
		$headline->setAttribute('priority', 3);
		$wrapper->appendChild($this->dom->createElement('paragraph', $text));
		$wrapper->appendChild($link = $this->dom->createElement('link', $email));
		$link->setAttribute('url', 'mailto:'.$email);
	}
}