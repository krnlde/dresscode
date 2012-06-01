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
 * @package		Dresscode
 */
namespace Dresscode;

/**
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Dresscode
 */
class Router
{
	/**
	 * @var \Dresscode\Application
	 */
	protected $Application;

	/**
	 * @var \Dresscode\Input
	 */
	protected $Input;

	/**
	 * @var \Dresscode\Request
	 */
	protected $Request;

	/**
	 * @var \Dresscode\Response
	 */
	protected $Response;

	/**
	 * @var array
	 */
	protected $options = array();

	public function __construct(Application $Application, array $options = array())
	{
		$this->Application	= $Application;
		$this->options	= (object)$options; // array to StdClass object to access properties via $options->$key instead of $options[$key], which is more comfortable
		$this->Input	= Input::getInstance();
		$this->Request	= Request::getInstance();
		$this->Response	= Response::getInstance();
	}

	public function handleRequests()
	{
		$path		= $this->Request->path;
		$rawPath	= $this->Request->queryString()->get('path');
		$this->Application->setFormat($this->Request->format);
		if (strlen($path) <= 1)
		{
			$this->Response->redirect($this->Application->defaultRoute(), 301); // 301 = Moved Permanently (recommended in production), 307 = Temporary Redirect (good for debugging)
		}
		if ($rawPath[strlen($rawPath) - 1] === '/') // if last character is '/', which is not allowed because of duplicate content
		{
			$basepath = Application::basePath();
			$this->Response->redirect($basepath.$path.(Application::getFormat() !== Application::DEFAULTFORMAT ? '.'.Application::getFormat() : ''), 301);
		}

		$this->Response->Header->contentType(Application::getFormat(), 'UTF-8'); // @todo obsolete in PHP 5.4

		// HTTP methods
		switch (strtolower($this->Request->method))
		{
			case 'post':
				$this->Application->post($path, $this->Input->post); // Posts contents which are handled inside the resource.
			break;
			case 'put':
				$this->Application->put($path, $this->Input->put); // creates a new or overwrites an existing resource.
			break;
			case 'delete':
				$this->Application->delete($path); // Deletes a resource.
			break;
			case 'options':
				$this->Application->options($path); // Returns options for this resource.
			break;
			case 'head':
				$this->Application->head($path, $this->Input->get); // Gets headers for a resource.
			break;
			default:
				$this->Application->get($path, $this->Input->get); // Gets the resource.
			break;
		}
	}
}