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
 * Contains HTTP request relevant data.
 *
 * All protected methods are readable as readonly!
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
class Request
{
	/**
	 * Singleton Instance.
	 *
	 * @var \Mocovi\Request
	 */
	protected static $instance;

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var string
	 */
	protected $scheme;

	/**
	 * @var string
	 */
	protected $domain;

	/**
	 * @var string
	 */
	protected $port;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $format;

	/* @todo
	$client
		->ip
		->browser
			->version
			->msie|mozilla|webkit|opera
	*/

	/**
	 * @var \StdClass
	 */
	protected $Header;

	protected $uri;

	/**
	 * @var date
	 * @todo move to Request::$header var
	 */
	protected $if_modified_since;

	protected function __construct()
	{
		$pathinfo					= (object)pathinfo('/'.$this->queryString()->get('path'));

		$this->method				= strtolower($_SERVER['REQUEST_METHOD']);
		$this->scheme				= isset($_SERVER['HTTPS']) ? 'https' : 'http';
		$this->domain				= $_SERVER['SERVER_NAME'];
		$this->port					= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER['SERVER_PORT'] : null;
		$this->path					= rtrim(str_replace('\\', '/', $pathinfo->dirname), '/').'/'.$pathinfo->filename;
		$this->format				= isset($pathinfo->extension) ? $pathinfo->extension : null;
		$this->if_modified_since	= isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : null;

		$this->Header = new \StdClass();
		foreach ($_SERVER as $key => $value)
		{
			if (substr($key, 0, 5) === 'HTTP_')
			{
				$this->Header->{strtolower(substr($key, 5))} = $value;
			}
		}
	}

	/**
	 * Singleton pattern constructor method.
	 *
	 * @return \Mocovi\Request $this
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return \Mocovi\HTTPQueryString
	 */
	public function queryString()
	{
		return HTTPQueryString::getInstance();
	}

	/**
	 * Class property getter.
	 *
	 * @magic
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->$name))
		{
			return $this->$name;
		}
	}

	/**
	 * Class property setter.
	 *
	 * @magic
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 * @throws \Mocovi\Exception\NotAllowed
	 */
	public function __set($name, $value)
	{
		throw new Exception\NotAllowed($name);
	}
}