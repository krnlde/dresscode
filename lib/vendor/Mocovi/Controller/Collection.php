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
namespace Mocovi\Controller;

use \Mocovi\Event;

/**
 * This collection contains a set of controllers.
 *
 * It also acts as a proxy for all methods, which means it forwards all method calls.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
class Collection implements \Iterator
{
	/**
	 * @var array of \Mocovi\Controller
	 */
	protected $set;

	private $position = 0;
	/**
	 * Constructor initializes the controller set.
	 *
	 * @param array $set Default: array();
	 * @return void
	 */
	public function __construct(array $set = array())
	{
		$this->set = $set;
	}

	/**
	 * Returns one controller out of the set.
	 *
	 * @param int $index
	 * @return \Mocovi\Controller
	 */
	public function get($index)
	{
		if (isset($this->set[$index]))
		{
			return $this->set[$index];
		}
		return null;
	}

	/**
	 * Magic caller proxy.
	 *
	 * Forwards all method calls to the controllers in the {@see $set}.
	 *
	 * @param string $method
	 * @param array $args
	 * @return \Mocovi\Controller\Collection $this
	 */
	public function __call($method, array $args)
	{
		foreach ($this->set as $controller)
		{
			if (method_exists($controller, $method))
			{
				call_user_func_array(array($controller, $method), $args);
			}
		}
		return $this;
	}

	/**
	 * Magic getter for class properties
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function __get($var)
	{
		if ($var === 'length')
		{
			return count($this->set);
		}
	}

	/**
	 * Iterator rewind method.
	 *
	 * @return void
	 */
	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * Iterator current method.
	 *
	 * @return \Mocovi\Controller
	 */
	public function current()
	{
		return $this->set[$this->position];
	}

	/**
	 * Iterator key method.
	 *
	 * @return integer
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Iterator next method.
	 *
	 * @return void
	 */
	public function next()
	{
		++$this->position;
	}

	/**
	 * Iterator valid method.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return isset($this->set[$this->position]);
	}
}