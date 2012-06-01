<?php
namespace Dresscode\Controller;

class Evaluate extends \Dresscode\Controller
{
	public function setup()
	{
		$this->on('launchChild', function ($event) {
			$event->preventDefault(); // block all children
		});
	}
	public function get(array $params = array())
	{
		ob_start();
		if (@eval($this->sourceNode->nodeValue) === false)
		{
			$error = (object)error_get_last();
			$this->errorHandler($error->type, $error->message, $error->file, $error->line);
		}
		$value = ob_get_clean();
		if ($value)
		{
			$this->node->appendChild($this->dom->createTextNode($value));
		}
	}

	public function errorHandler($errno, $errstr, $errfile = __FILE__, $errline = __LINE__, array $errcontext = array())
	{
		$this->error(new \ErrorException($errstr, $errno, 1, $errfile, $errline));
	}
}