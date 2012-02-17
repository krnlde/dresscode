<?php
namespace Mocovi\Controller;

class Filter extends \Mocovi\Controller
{
	/**
	 * @property
	 * @hideIfEmpty
	 */
	protected $require;

	/**
	 * @property
	 * @hideIfEmpty
	 */
	protected $skip;

	protected function before(array $params = array())
	{
		parent::before($params);
		$self = $this;
		$this->parent->on('loadFile', function($event) use ($self) {
			if ($self->require)
			{
				if (preg_match($self->require, $event->relatedTarget->getFileName()))
				{
					$event->stopPropagation();
					return true;
				}
				else
				{
					return $event->result;
				}
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