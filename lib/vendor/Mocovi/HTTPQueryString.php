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
class HTTPQueryString
{
	protected static $instance;
	protected $queryString = array();

	protected function __construct()
	{
		if(strlen($_SERVER['QUERY_STRING']) > 0)
			foreach(explode('&', $_SERVER['QUERY_STRING']) as $element)
			{
				$explode = explode('=', $element, 2);
				$this->queryString[$explode[0]] = isset($explode[1]) ? $explode[1] : null;
			}
	}

	public static function getInstance()
	{
		if(is_null(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}

	public function set(array $array)
	{
		foreach($array as $key => $value)
			$this->queryString[$key] = $value;
		return $this;
	}

	public function get($key = null)
	{
		if(isset($this->queryString[$key]))
			return $this->queryString[$key];
		$a = array();
		foreach($this->queryString as $key => $value)
			$a[] = $key.'='.$value;
		return implode('&', $a);
	}

	public function toArray()
	{
		return $this->queryString;
	}

	public function toString()
	{
		$array = array();
		foreach($this->queryString as $key => $value)
			$array[] = $key.'='.$value;
		return implode('&', $array);
	}

	public function __toString()
	{
		return $this->toString();
	}
}