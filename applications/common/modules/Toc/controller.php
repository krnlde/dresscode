<?php
namespace Mocovi\Controller;

class Toc extends \Mocovi\Controller
{

	/**
	 * @property
	 * @var string
	 */
	public $xpath = '//headline[@id]';

	protected function before(array $params = array())
	{
		$self = $this;
		$this->closest('Root')->on('ready', function ($event) use ($self) {
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