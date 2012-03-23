<?php
namespace Mocovi\Controller;

\Mocovi\Module::requireController('Plain');

class Variable extends Plain
{
	/**
	 * @property
	 * @var string
	 */
	protected $name;
}