<?php
namespace Mocovi\Controller;

class Evaluate extends \Mocovi\Controller
{
	public function setup()
	{
		$this->on('launchChild', function ($event) {
			$event->preventDefault(); // block all children
		});
	}
	public function get(array $params = array())
	{
		if (@eval($this->sourceNode->nodeValue) === false)
		{
			$error = (object)error_get_last();
			$this->errorHandler($error->type, $error->message, $error->file, $error->line);
		}
	}

	public function errorHandler($errno, $errstr, $errfile = __FILE__, $errline = __LINE__, array $errcontext = array())
	{
		$this->error(new \ErrorException($errstr, $errno, 1, $errfile, $errline));
	}
}