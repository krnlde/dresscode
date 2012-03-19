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
class Response
{
	/**
	 * Singleton Instance.
	 *
	 * @var \Mocovi\Response
	 */
	protected static $instance;

	/**
	 * @var \Mocovi\ResponseHeader
	 */
	public $Header;

	/**
	 * Singleton constructor set protected to deny direct access.
	 *
	 * @return void
	 */
	protected function __construct()
	{
		$this->Header = ResponseHeader::getInstance();
	}

	/**
	 * Singleton method to get the instance.
	 *
	 * @return \Mocovi\Response $this
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
	 * @param string $path
	 * @param int $code HTTP Status Code; Default: 307 (Temporary Redirect); Otherise use 301 (Moved Permanently)
	 * @return void
	 */
	public function redirect($path, $code = 307) // 307 = Temporary Redirect
	{
		if (!in_array($code, array(301, 307)))
		{
			$code = 307; // Temporary Redirect
		}

		if ($path === 'back')
		{
			if (isset($_SERVER['HTTP_REFERER']))
			{
				$path = $_SERVER['HTTP_REFERER'];
			}
			else
			{
				throw new Exception\Redirect('Cannot redirect back because $_SERVER[\'HTTP_REFERER\'] was not defined. You\'ll have to define a redirect by yourself.');
			}
		}
		$this->Header->location($path, $code);
		exit;
	}

	/**
	 * @param string $data
	 * @param int $code HTTP Status Code; Default: 200 (OK);
	 * @return void
	 */
	public function write($data, $code = 200)
	{
		$this->Header->status($code);
		$this->Header->send();
		echo $data;
	}

	/**
	 * Dies after execution (ambivalence intended).
	 *
	 * @param string $data
	 * @param int $code HTTP Status Code; Default: 200 (OK);
	 * @return void
	 */
	public function end($data = '', $code = 200)
	{
		$this->Header->status($code);
		$this->Header->send();
		if (!empty($data))
		{
			echo $data;
		}
		if (isset($_GET['debug']) && \Mocovi\Application::getFormat() === 'html')
		{
			echo '<!-- parsed in: '.round(microtime(true) - STARTTIME, 4).'s -->'; // @debug
		}
		exit;
	}
}