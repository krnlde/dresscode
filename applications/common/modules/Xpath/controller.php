<?php
namespace Mocovi\Controller;

\Mocovi\Module::requireController('Plain');

class Xpath extends \Mocovi\Controller\Plain
{
	/**
	 * @property
	 * @var string
	 */
	protected $query;

	protected function get(array $params = array())
	{
		$self	= $this;
		$query	= $this->query;
		$this->closest('Root')->on('ready', function ($event) use ($self, $query) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			$xpath	= new \DOMXPath($self->getDom());
			$result	= @$xpath->evaluate($query, $self->getNode());
			if ($result === false)
			{
				$this->error(new \Mocovi\Exception\WrongFormat('xpath'));
				return;
			}
			if ($result)
			{
				$self->setText(is_object($result) ? $result->item(0)->nodeValue : $result);
			}
			else
			{
				$self->setText('[null]');
			}
		});
	}
}