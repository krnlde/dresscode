<?php
/**
 *  Copyright (C) 2011 Kai Dorschner
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @copyright	Copyright 2011, Kai Dorschner
 * @license		http://www.gnu.org/licenses/gpl.html GPLv3
 * @package		Mocovi
 */
namespace Mocovi;

use \Mocovi\Event;

/**
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
class Observable
{
	/**
	 * Callbacks from the Observers / Event Handlers are saved in here.
	 *
	 * @var array
	 */
	private $eventHandlers = array();

	/**
	 * Target which will be attached to the Event.
	 *
	 * @var mixed
	 */
	private $target;

	/**
	 * Modifies the default target, which is $this.
	 *
	 * @param mixed $target
	 * @return \Mocovi\Observable $this
	 */
	public function setTarget($target)
	{
		$this->target = $target;
		return $this;
	}

	/**
	 * Invokes all observing callbacks matching the event.
	 *
	 * @param string $type
	 * @param mixed $relatedTarget
	 * @param array $data Default: array();
	 * @return \Mocovi\Event
	 */
	final public function trigger($type, $relatedTarget = null, array $data = array())
	{
		$event = new Event($type, ($this->target ?: $this), $relatedTarget, $data);
		if (isset($this->eventHandlers[$type]))
		{
			foreach ($this->eventHandlers[$type] as $callback)
			{
				if (!$event->isPropagationStopped())
				{
					$event->result = $callback($event);
				}
			}
		}
		return $event;
	}

	/**
	 * Sets a callback listener (Observer) for an event type.
	 *
	 * @param $string $type
	 * @param \Closure|array $callback
	 * @return \Mocovi\Observable $this
	 * @throws \Mocovi\Exception\NotAllowed
	 */
	final public function on($type, $callback)
	{
		if (is_callable($callback))
		{
			$this->eventHandlers[$type][] = $callback;
			return $this;
		}
		throw new Exception\NotAllowed('Second argument has a wrong callback type.');
	}
}