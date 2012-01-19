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
	protected $Routable;
	protected $Input;
	protected $Request;
	protected $Response;
	protected $options = array();

	public function __construct(Routable $Routable, array $options = array())
	{
		$this->Routable	= $Routable;
		$this->options	= (object)$options; // array to StdClass object to access properties via $options->$key instead of $options[$key], which is more comfortable
		$this->Input	= Input::getInstance();
		$this->Request	= Request::getInstance();
		$this->Response	= Response::getInstance();
	}

	public function handleRequests()
	{
		$path		= $this->Request->path;
		$rawPath	= $this->Request->queryString()->get('path');
		$format		= $this->Request->format;
		if (strlen($path) <= 1)
		{
			$this->Response->redirect($this->Routable->defaultRoute(), 307); // 301 = Moved Permanently, 307 = Temporary Redirect
		}
		if ($rawPath[strlen($rawPath) - 1] === '/') // if last character is '/', which is not allowed because of duplicate content
		{
			$basepath = Mocovi::basePath();
			$this->Response->redirect($basepath.$path.($format ? '.'.$format : ''), 301);
		}

		$this->Response->Header->contentType($format, 'UTF-8');

		// HTTP methods
		switch (strtolower($this->Request->method))
		{
			default /* get, head */:
				$this->Routable->get($path, $format, $this->Input->get);
			break;
			case 'post':
				$this->Routable->post($path, $format, $this->Input->post);
			break;
			case 'put':
				$this->Routable->put($path, $format, $this->Input->put);
			break;
			case 'delete':
				$this->Routable->delete($path);
			break;
			case 'options':
				$this->Routable->options($path);
			break;
		}
	}
}