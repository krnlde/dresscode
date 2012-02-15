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

/**
 * This is a simple Event inspired by jQuery event object {@link http://api.jquery.com/category/events/event-object/}.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 * @todo		implement preventDefault?
 */
class Event
{
	/**
	 * The last value returned by an event handler that was triggered by this event, unless the value was null.
	 *
	 * @var mixed
	 */
	public $result;

	/**
	 * Describes the nature of the event.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The object that initiated the event.
	 *
	 * @var \Mocovi\Observable
	 */
	private $target;

	/**
	 * An object involved in the event, if any.
	 *
	 * @var object
	 */
	private $relatedTarget;

	/**
	 * An optional data map passed to an event method when the current executing handler is bound.
	 *
	 * @var array
	 */
	private $data;

	private $default		= true;
	private $propagation	= true;

	/**
	 * Constructor method initially setting event type.
	 *
	 * @param string $type
	 * @param mixed $target
	 * @param mixed $relatedTarget
	 * @param array $data Default: array();
	 * @return void
	 */
	public function __construct($type, $target, $relatedTarget = null, array $data = array())
	{
		$this->type				= $type;
		$this->target			= $target;
		$this->relatedTarget	= $relatedTarget;
		$this->data				= $data;
	}

	/**
	 * If this method is called, the default action of the event will not be triggered.
	 *
	 * Sets the {@see $default} variable to true.
	 *
	 * @return \Mocovi\Event
	 */
	public function preventDefault()
	{
		$this->default = false;
		return $this;
	}

	/**
	 * Returns whether {@see preventDefault()} was ever called on this event object.
	 *
	 * @return boolean
	 */
	public function isDefaultPrevented()
	{
		return !$this->default;
	}

	/**
	 * Prevents the event from bubbling up the DOM tree, preventing any parent handlers from being notified of the event.
	 *
	 * @return \Mocovi\Event $this
	 */
	public function stopPropagation()
	{
		$this->propagation = false;
		return $this;
	}

	/**
	 * Returns whether {@see stopPropagation()} was ever called on this event object.
	 *
	 * @return boolean
	 */
	public function isPropagationStopped()
	{
		return !$this->propagation;
	}

	/**
	 * This method disallowes setting protected and private variables.
	 *
	 * @magic
	 * @param string $var
	 * @param mixed $value
	 * @return void
	 * @throws \Mocovi\Exception\NotAllowed
	 */
	public function __set($var, $value)
	{
		throw new \Mocovi\Exception\NotAllowed('Set variable');
	}

	/**
	 * This method lets you readprotected and private variables.
	 *
	 * @magic
	 * @param string $var
	 * @return mixed
	 */
	public function __get($var)
	{
		if (property_exists($this, $var))
		{
			return $this->$var;
		}
		return null;
	}
}