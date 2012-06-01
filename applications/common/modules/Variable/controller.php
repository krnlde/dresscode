<?php
namespace Dresscode\Controller;

\Dresscode\Module::requireController('Plain');

class Variable extends Plain
{
	/**
	 * @property
	 * @var string
	 */
	protected $name;
}