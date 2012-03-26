<?php
namespace Mocovi\Controller;

class Filter extends \Mocovi\Controller
{
	/**
	 * @property
	 * @hideIfEmpty
	 * @var string
	 */
	protected $require;

	/**
	 * @property
	 * @hideIfEmpty
	 * @var string
	 */
	protected $skip;

	/**
	 * @todo implement $event to filter a specific event. Default would be "loadFile".
	 * @var string
	 */
	// protected $event;

	public function setup()
	{
		$self = $this;
		$this->parent->on('loadFile', function($event) use ($self) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			if ($self->require && !preg_match($self->require, $event->relatedTarget->getFileName()))
			{
				$event->stopPropagation();
				return false;
			}
			if ($self->skip && preg_match($self->skip, $event->relatedTarget->getFileName()))
			{
				$event->stopPropagation();
				return false;
			}
			return true;
		});
	}

	protected function createNode()
	{
		return $this->dom->createComment($this->getName());
		// return $this->dom->createComment($this->getName().' -'.($this->require ? ' require: '.$this->require : '').($this->skip ? ' skip: '.$this->skip : '')); // @todo security issue?
	}

	/**
	 * Getter for protected properties
	 *
	 * @magic
	 */
	public function __get($var)
	{
		if (array_key_exists($var, $this->getProperties()))
		{
			return $this->$var;
		}
	}
}