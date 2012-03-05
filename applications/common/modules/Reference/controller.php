<?php
namespace Mocovi\Controller;

\Mocovi\Module::requireController('Plain');

class Reference extends \Mocovi\Controller\Plain
{
	/**
	 * @property
	 * @var string
	 */
	protected $xpath;

	protected function get(array $params = array())
	{
		$self	= $this;
		$query	= $this->xpath;
		$this->closest('Root')->on('ready', function ($event) use ($self, $query) {
			$xpath	= new \DOMXPath($self->getDom());
			$result	= $xpath->query($query);
			if ($result->length > 0)
			{
				$self->setText($result->item(0)->nodeValue);
			}
			else
			{
				$self->setText('[null]');
			}
		});
	}
}