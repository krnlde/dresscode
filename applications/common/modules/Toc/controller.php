<?php
namespace Mocovi\Controller;

class Toc extends \Mocovi\Controller
{

	/**
	 * @property
	 * @var string
	 */
	public $xpath = '//headline[@id]';

	public function setup()
	{
		$self = $this;
		$this->closest('Root')->on('ready', function ($event) use ($self) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			$dom	= $self->getDom();
			$node	= $self->getNode();
			$xpath	= new \DOMXPath($dom);
			foreach ($xpath->query($self->xpath) as $headline)
			{
				$node->appendChild($element = $dom->createElement('element', $headline->nodeValue));
				$element->setAttribute('id', urlencode(trim($headline->nodeValue)));
			}
		});
	}
}