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
	private $type;
	private $target	= null;
	private $data	= array();

	/**
	 * Constructor method initially setting event type.
	 *
	 * @param string $type
	 * @param mixed $target
	 * @param array $data Default: array();
	 * @return void
	 */
	public function __construct($type, $target = null, array $data = array())
	{
		$this->type		= $type;
		$this->target	= $target;
		$this->data		= $data;
	}

	/**
	 * This method disallowes setting protected and private variables.
	 *
	 * @magic
	 * @param string $var
	 * @param mixed $value
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