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
class Router
{
	/**
	 * @var \Mocovi\Application
	 */
	protected $Application;

	/**
	 * @var \Mocovi\Input
	 */
	protected $Input;

	/**
	 * @var \Mocovi\Request
	 */
	protected $Request;

	/**
	 * @var \Mocovi\Response
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
			$this->Response->redirect($this->Application->defaultRoute(), 307); // 301 = Moved Permanently, 307 = Temporary Redirect
		}
		if ($rawPath[strlen($rawPath) - 1] === '/') // if last character is '/', which is not allowed because of duplicate content
		{
			$basepath = Application::basePath();
			$this->Response->redirect($basepath.$path.(Application::getFormat() !== Application::DDEFAULTFORMAT ? '.'.Application::getFormat() : ''), 301);
		}

		$this->Response->Header->contentType(Application::getFormat(), 'UTF-8'); // @todo obsolete in PHP 5.4

		// HTTP methods
		switch (strtolower($this->Request->method))
		{
			case 'post':
				$this->Application->post($path, $this->Input->post);
			break;
			case 'put':
				$this->Application->put($path, $this->Input->put);
			break;
			case 'delete':
				$this->Application->delete($path);
			break;
			case 'options':
				$this->Application->options($path);
			break;
			case 'head':
				$this->Application->head($path, $this->Input->get);
			break;
			default:
				$this->Application->get($path, $this->Input->get);
			break;
		}
	}
}