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
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
class Observer
{
	private $callbacks = array();

	final public function update(\Mocovi\Observable $source, $message)
	{
		if (isset($this->callbacks[$message]))
		{
			foreach ($this->callbacks[$message] as $callback)
			{
				if (is_callable($callback))
				{
					$callback($source);
				}
			}
		}
	}

	final public function on($message, $callback)
	{
		if (is_callable($callback))
		{
			$this->callbacks[$message][] = $callback;
			return $this;
		}
		throw new Exception('Wrong callback type.');
	}
}