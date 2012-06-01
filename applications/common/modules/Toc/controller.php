<?php
namespace Dresscode\Controller;

class Toc extends \Dresscode\Controller
{

	/**
	 * @property
	 * @hidden
	 * @var string
	 */
	public $xpath = '//article//*[name()="header" or name()="section"]/headline[@id]';

	public function setup()
	{
		$self = $this;
		$this->closest('Root')->on('ready', function ($event) use ($self) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			$dom	= $self->getDom();
			$node	= $self->getNode();
			$xpath	= new \DOMXPath($dom);
			foreach ($xpath->query($self->xpath) as $headline)
			{
				$node->appendChild($element = $dom->createElement('element', $xpath->query('./text()', $headline)->item(0)->nodeValue));
				$element->setAttribute('id', $headline->getAttribute('id'));
			}
		});
	}
}