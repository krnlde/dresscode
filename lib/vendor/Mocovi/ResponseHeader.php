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
 * Modifies HTTP Response Header
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
class ResponseHeader
{
	/**
	 * Default HTTP status code.
	 *
	 * @var integer
	 */
	protected $statuscode = 200;

	/**
	 * Associative array where the key is the code and the value is the definition.
	 *
	 * @var array of string
	 */
	protected $statuscodes = array
		( 100 => 'Continue'
		, 101 => 'Switching Protocols'

		, 200 => 'OK'
		, 201 => 'Created'
		, 202 => 'Accepted'
		, 203 => 'Non-Authoritative Information'
		, 204 => 'No Content'
		, 205 => 'Reset Content'
		, 206 => 'Partial Content'

		, 300 => 'Multiple Choices'
		, 301 => 'Moved Permanently'
		, 302 => 'Found'
		, 303 => 'See Other'
		, 304 => 'Not Modified'
		, 305 => 'Use Proxy'
		, 307 => 'Temporary Redirect'

		, 400 => 'Bad Request'
		, 401 => 'Unauthorized'
		, 402 => 'Payment Required'
		, 403 => 'Forbidden'
		, 404 => 'Not Found'
		, 405 => 'Method Not Allowed'
		, 406 => 'Not Acceptable'
		, 407 => 'Proxy Authentication Required'
		, 408 => 'Request Timeout'
		, 409 => 'Conflict'
		, 410 => 'Gone'
		, 411 => 'Length Required'
		, 412 => 'Precondition Failed'
		, 413 => 'Request Entity Too Large'
		, 414 => 'Request-URI Too Long'
		, 415 => 'Unsupported Media Type'
		, 416 => 'Requested Range Not Satisfiable'
		, 417 => 'Expectation Failed'

		, 500 => 'Internal Server Error'
		, 501 => 'Not Implemented'
		, 502 => 'Bad Gateway'
		, 503 => 'Service Unavailable'
		, 504 => 'Gateway Timeout'
		, 505 => 'HTTP Version Not Supported'
		);

	/**
	 * Mime Type mapping.
	 *
	 * Associative array where the key is the raw file type and the value is the
	 * correct mime type.
	 *
	 * @var array of string
	 * @todo is there a better solution? Maybe a mime type object.
	 */
	protected $knownMimeTypes = array
		( 'json'	=> 'application/json'
		, 'text'	=> 'text/plain'
		, 'xml'		=> 'text/xml'
		, 'html'	=> 'text/html'
		);

	/**
	 * Singleton Instance.
	 *
	 * @var \Mocovi\ResponseHeader
	 */
	protected static $instance;

	/**
	 * Tells if the response header is already sent.
	 *
	 * @var boolean
	 */
	protected $sent = false;

	/**
	 * Internal header buffer.
	 *
	 * Saves all header strings.
	 *
	 * @var array
	 */
	protected $buffer = array();

	// protected $cookies = array();

	/**
	 * Singleton constructor set protected to deny direct access.
	 *
	 * @return void
	 */
	protected function __construct()
	{
		if(!($this->sent = headers_sent()))
		{
			header_remove('X-Powered-By'); // Clear PHP version for security reasons
		}
	}

	/**
	 * Finally sends headers (if not already done).
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->send();
	}


	/**
	 * Singleton method to get the instance.
	 *
	 * @return \Mocovi\ResponseHeader $this
	 */
	public static function getInstance()
	{
		if(!isset($instance))
			self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Sends the response header.
	 *
	 * @return boolean Success
	 */
	public function send()
	{
		if(!$this->sent && !headers_sent())
		{
			header('HTTP/1.1 '.$this->statuscode.' '.$this->statuscodes[$this->statuscode]); // alternatively http_response_code ([ int $response_code ] )
			foreach($this->buffer as $key => $value)
				if(strlen($value))
					header($key.': '.$value);
				else
					header($key);
			//$this->sendCookies();
			return true;
		}
		$this->sent = true;
		return false;
	}

	/**
	 * @unused
	 */
	// public function sendCookies()
	// {
	// 	$return = true;
	// 	foreach($this->cookiesBuffer as $cookie)
	// 		$return &= setcookie($cookie['name'], $cookie['value'], $cookie['expire']);
	// 	return $return;
	// }

	// public function addCookie($name, $value, $expire)
	// {
	// 	$this->cookiesBuffer[] = array
	// 		( 'name'	=> $name
	// 		, 'value'	=> $value
	// 		, 'expire'	=> $expire
	// 		);
	// }

	public function notModified()
	{
		$this->status(304);
		return $this;
	}

	public function location($location, $code = 307 ) // 307 = temporary redirect
	{
		$this->status($code);
		$this->add('Location', $location);
		$this->send();
		die();
		return $this;
	}

	public function contentType($media, $charset = '')
	{
		if(isset($this->knownMimeTypes[$media]))
		{
			$media = $this->knownMimeTypes[$media];
		}
		$this->add('Content-Type', $media.(empty($charset) ? '': '; charset='.$charset));
		return $this;
	}

	public function contentDisposition($filename, $disposition = 'inline')
	{
		$this->add('Content-Disposition', $disposition.'; filename="'.$filename.'"');
		return $this;
	}

	public function contentLength($length)
	{
		$this->add('Content-Length', (int)$length);
		return $this;
	}

	public function status($statuscode)
	{
		if(isset($this->statuscodes[$statuscode]))
			$this->statuscode = $statuscode;
		return $this;
	}

	public function lastModified($unixtime)
	{
		$this->add('Last-Modified', gmdate('D, d M Y H:i:s', $unixtime).' GMT');
		return $this;
	}

	public function expires($unixtime)
	{
		$this->add('Pragma', 'public');
		$this->add('Cache-Control', 'maxage='.($unixtime - time()));
		$this->add('Expires', gmdate('D, d M Y H:i:s', $unixtime).' GMT');
		return $this;
	}


	public function etag($etag)
	{
		$this->add('Etag', $etag);
		return $this;
	}

	public function add($type, $value = '')
	{
		if(!($this->sent = headers_sent()))
		{
			$this->buffer[$type] = $value;
			return $this;
		}
		throw new \Mocovi\Exception('Cannot add a buffer. Response Header already sent.');
	}

	public function remove($type)
	{
		if(!($this->sent = headers_sent()))
		{
			if (array_key_exists($type, $this->buffer))
			{
				unset($this->buffer[$type]);
			}
			return $this;
		}
		throw new \Mocovi\Exception('Cannot remove a buffer. Response Header already sent.');
	}
}