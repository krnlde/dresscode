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
class Input
{
	protected static $instance;

	protected $get	= array();
	protected $post	= array();
	protected $put	= array();

	protected function __construct()
	{
		$this->get	= $_GET;
		unset($this->get['path']);
		unset($this->get['debug']);
		$this->post	= $_POST;
		// @todo put
	}

	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get($name)
	{
		if (array_key_exists($name, $this->get))
		{
			return $this->get[$name];
		}
		return null;
	}

	public function post($name)
	{
		if (array_key_exists($name, $this->post))
		{
			return $this->post[$name];
		}
		return null;
	}

	public function put($name)
	{
		if (array_key_exists($name, $this->put))
		{
			return $this->put[$name];
		}
		return null;
	}

	public function __get($name)
	{
		if (in_array($name, array('get', 'post', 'put')))
		{
			return $this->$name;
		}
		return null;
	}

	public function __set($name, $value)
	{
		throw new Exception\NotAllowed($name);
	}
}