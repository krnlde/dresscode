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

use Assetic\Asset\AssetCache;
use Assetic\Factory\AssetFactory;
use Assetic\FilterManager;
use Assetic\AssetWriter;
use Assetic\Filter;
use Assetic\Cache\FilesystemCache;

/**
 * Handles HTTP requests and provides interfaces for all important HTTP methods.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
class Application implements Routable
{
	/**
	 * @var \Mocovi\Model
	 */
	public $Model;

	/**
	 * @var \Mocovi\Request
	 */
	public $Request;

	/**
	 * @var \Mocovi\Response
	 */
	public $Response;

	/**
	 * @var \Mocovi\Router
	 */
	public $Router;

	public $defaultRoute = 'index';

	protected $defaultModules = array
		( 'root'
		, 'headline'
		, 'paragraph'
		, 'listing'
		, 'cite'
		);

	/**
	 * Path to the Application Pool.
	 *
	 * @var \DirectoryIterator
	 */
	protected static $pool;

	/**
	 * The actual "domain" of the application.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * @var \DomDocument
	 */
	protected $dom;

	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $statuscode = 200;


	protected static $stylesheets = array();
	protected static $javascripts = array();

	private static $AssetWriter;

	/**
	 * @param \DirectoryIterator $applicationPool Pool where to find applications by name
	 * @param array $options Default: array()
	 */
	public function __construct(\DirectoryIterator $applicationPool, array $options = array())
	{
		session_start();
		self::$pool					= $applicationPool;
		$this->Request				= Request::getInstance();
		$this->Response				= Response::getInstance();
		$this->name					= isset($options['name']) ? $options['name'] : $this->Request->domain;
		$this->Router				= new Router(/* $Routable */ $this, /* $options */ $_SERVER);
		$this->resetDom();
		Module::initialize($this->getPath(), $this->getCommonPath(), $this->dom);
		foreach($this->defaultModules as $module)
		{
			if (file_exists($templatePath = Module::findTemplates($module)))
			{
				Module::getView()->addTemplatePool($templatePath);
			}
		}
		set_error_handler(array($this, 'errorHandler'));
		set_exception_handler(array($this, 'exceptionHandler'));
		$modelPath		= $this->getModelPath();
		$this->Model	= new Model\XML($modelPath);
		if (file_exists($bootstrap = $this->getPath()->getPath().DIRECTORY_SEPARATOR.'bootstrap.php'))
		{
			include $bootstrap;
		}
	}

	/**
	 * @return string
	 */
	public static function basePath()
	{
		return dirname($_SERVER['SCRIPT_NAME']);
	}

	/**
	 * Resets the {@see $dom} (creates a new DomDocument).
	 */
	public function resetDom()
	{
		$this->dom						= new \DomDocument('1.0', 'utf-8');
		$this->dom->preserveWhiteSpace	= false;
		$this->dom->formatOutput		= false;
		return $this;
	}

	/**
	 * @return \Mocovi\Router
	 */
	public function getRouter()
	{
		return $this->Router;
	}

	/**
	 * @return string default route
	 */
	public function defaultRoute()
	{
		return $this->defaultRoute;
	}

	/**
	 * @return \DirectoryIterator
	 */
	public function getPath()
	{
		if (file_exists($path = self::$pool->getPath().DIRECTORY_SEPARATOR.$this->name))
		{
			return new \DirectoryIterator($path);
		}
		return $this->getCommonPath();

	}

	/**
	 * @return \DirectoryIterator
	 */
	public static function getCommonPath()
	{
		return new \DirectoryIterator(self::$pool->getPath().DIRECTORY_SEPARATOR.'common');
	}

	/**
	 * @return \DirectoryIterator
	 */
	public function getModelPath()
	{
		return new \DirectoryIterator($this->getPath()->getPath().DIRECTORY_SEPARATOR.'models');
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * HTTP request method HEAD.
	 *
	 * The HEAD method is identical to GET except that the server MUST NOT
	 * return a message-body in the response.
	 * The metainformation contained in the HTTP headers in response to a
	 * HEAD request SHOULD be identical to the information sent in response to
	 * a GET request. This method can be used for obtaining metainformation
	 * about the entity implied by the request without transferring the
	 * entity-body itself. This method is often used for testing hypertext links
	 * for validity, accessibility, and recent modification.
	 *
	 * The response to a HEAD request MAY be cacheable in the sense that the
	 * information contained in the response MAY be used to update a previously
	 * cached entity from that resource. If the new field values indicate that
	 * the cached entity differs from the current entity (as would be indicated
	 * by a change in Content-Length, Content-MD5, ETag or Last-Modified),
	 * then the cache MUST treat the cache entry as stale.
	 * (source: http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4)
	 *
	 * @todo implement
	 * @return void
	 * @param string $path
	 */
	public function head($path, $format, array $params = array())
	{
		$params['method']	= $this->Request->method;
		$params['scheme']	= $this->Request->scheme;
		$params['domain']	= $this->name;
		$params['port']		= $this->Request->port;
		$params['path']		= $this->Request->path;
		$params['format']	= $this->Request->format;
		try
		{
			$file	= $this->Model->read($path);
			$mtime	= $this->Model->lastModified($path);
			$params['modified']	= $mtime;

			// handle client side cache
			// @todo also see: If-Modified-Since, If-Unmodified-Since, If-Match and If-None-Match
			if ($mtime === $this->Request->if_modified_since)
			{
				$this->Response->end(null, 304); // Not modified
			}
			else
			{
				$this->Response->Header->lastModified($mtime);
			}

			if ($file->nodeName !== 'file')
			{
				throw new Exception\WrongFormat($path);
			}

			$methods = $file->getAttribute('methods');
			if ($methods)
			{
				$methods = explode(' ', str_replace('get', 'get head', strtolower($methods))); // if get is present, head should be too.
			}
			else
			{
				$methods = array('get', 'head'); // default methods.
			}

			if (!in_array(__FUNCTION__, $methods))
			{
				throw new Exception\WrongMethod(__FUNCTION__);
			}

			if (!is_null($file->getAttribute('statusCode')))
			{
				$this->statuscode = $file->getAttribute('statusCode');
			}

			if ($to = $file->getAttribute('redirect'))
			{
				if ($to[0] === '/')
				{
					$to = dirname($_SERVER['SCRIPT_NAME']).$to;
				}
				$this->Response->redirect($to, $this->statuscode);
			}

			isset($params['author'])
				or $params['author'] = $file->getAttribute('author');

			$params['title']	= $file->getAttribute('alias') ?: $file->getAttribute('name');

			// @todo implement partial GET (with "HTTP/1.1 206 Partial Content")

			$rootController	= $file->getElementsByTagNameNS(\Mocovi\Controller::NS, '*')->item(0); // first occuring controller
			$controller		= Module::createControllerFromNode($rootController);
			$controller->launch('get', $params, $this->dom, $this);
			// die($this->dom->saveXML()); // @debug
		}
		catch (Exception\FileNotFound $e)
		{
			$this->statuscode = 404; // File Not Found
			try
			{
				$file		= $this->Model->read('/404');
				$controller	= Module::createControllerFromNode($file->childNodes->item(0));
				$params['author']	= get_class($this);
				$params['title']	= '404';
				$controller->launch('get', $params, $this->dom, $this);
			}
			catch (Exception\FileNotFound $e2)
			{
				// @todo show info that no 404 file is defined
				$this->resetDom();
				$controller	= Module::createErrorController($e);
				$controller->launch('get', $params, $this->dom, $this);
			}
		}
		catch (Exception\WrongMethod $e)
		{
			// @todo test
			$this->statuscode = 405; // Method Not Allowed
			$controller	= Module::createErrorController($e);
			$controller->launch('get', $params, $this->dom, $this);
		}
		catch (Exception $e)
		{
			// @todo test
			$this->statuscode = 500; // Internal Server Error
			$controller	= Module::createErrorController($e);
			$controller->launch('get', $params, $this->dom, $this);
		}
		$this->Response->write(null, $this->statuscode);
	}

	/**
	 * HTTP request method GET.
	 *
	 * The GET method means retrieve whatever information (in the form of an
	 * entity) is identified by the Request-URI. If the Request-URI refers to
	 * a data-producing process, it is the produced data which shall be returned
	 * as the entity in the response and not the source text of the process,
	 * unless that text happens to be the output of the process.
	 *
	 * The semantics of the GET method change to a "conditional GET" if the
	 * request message includes an If-Modified-Since, If-Unmodified-Since,
	 * If-Match, If-None-Match, or If-Range header field. A conditional GET
	 * method requests that the entity be transferred only under the
	 * circumstances described by the conditional header field(s).
	 * The conditional GET method is intended to reduce unnecessary network
	 * usage by allowing cached entities to be refreshed without requiring
	 * multiple requests or transferring data already held by the client.
	 *
	 * The semantics of the GET method change to a "partial GET" if the request
	 * message includes a Range header field. A partial GET requests that only
	 * part of the entity be transferred, as described in section 14.35.
	 * The partial GET method is intended to reduce unnecessary network usage by
	 * allowing partially-retrieved entities to be completed without
	 * transferring data already held by the client.
	 *
	 * The response to a GET request is cacheable if and only if it meets the
	 * requirements for HTTP caching described in section 13.
	 * (source: http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3)
	 *
	 * @return void
	 * @param string $path
	 * @param array $params HTTP Params; Default: array()
	 */
	public function get($path, $format, array $params = array())
	{
		$this->head($path, $format, $params);
		$View = Module::getView();
		$View->addTemplatePool(Module::findTemplates('error'));
		$this->Response->end($View->transform($this->dom)->to($format), $this->statuscode);
	}

	/**
	 * HTTP request method POST.
	 *
	 * The POST method is used to request that the origin server accept the
	 * entity enclosed in the request as a new subordinate of the resource
	 * identified by the Request-URI in the Request-Line. POST is designed to
	 * allow a uniform method to cover the following functions:
	 * - Annotation of existing resources;
	 * - Posting a message to a bulletin board, newsgroup, mailing list,
	 *   or similar group of articles;
	 * - Providing a block of data, such as the result of submitting a
	 *   form, to a data-handling process;
	 * - Extending a database through an append operation.
	 *
	 * The actual function performed by the POST method is determined by the
	 * server and is usually dependent on the Request-URI. The posted entity is
	 * subordinate to that URI in the same way that a file is subordinate to a
	 * directory containing it, a news article is subordinate to a newsgroup to
	 * which it is posted, or a record is subordinate to a database.
	 *
	 * The action performed by the POST method might not result in a resource
	 * that can be identified by a URI. In this case, either 200 (OK) or
	 * 204 (No Content) is the appropriate response status, depending on whether
	 * or not the response includes an entity that describes the result.
	 *
	 * If a resource has been created on the origin server, the response SHOULD
	 * be 201 (Created) and contain an entity which describes the status of
	 * the request and refers to the new resource, and a Location header
	 * (see section {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.30 14.30}).
	 * (source: http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5)
	 *
	 *
	 * The URI in a POST request identifies the resource that will handle the enclosed entity.
	 * That resource might be a data-accepting process, a gateway to some
	 * other protocol, or a separate entity that accepts annotations.
	 * (source: http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.6)
	 *
	 * @todo implement
	 * @return void
	 * @param string $path
	 * @param array $params HTTP Params; Default: array()
	 */
	public function post($path, $format, array $params = array())
	{
		// @todo if resource is created add Header "201 Created" paired with a "Location: ...""
		$this->Response->end('HTTP POST', 501); // 501 Not Implemented
	}

	/**
	 * HTTP request method PUT.
	 *
	 * The PUT method requests that the enclosed entity be stored under
	 * the supplied Request-URI. If the Request-URI refers to an already
	 * existing resource, the enclosed entity SHOULD be considered as a modified
	 * version of the one residing on the origin server. If the Request-URI does
	 * not point to an existing resource, and that URI is capable of being
	 * defined as a new resource by the requesting user agent, the origin server
	 * can create the resource with that URI. If a new resource is created,
	 * the origin server MUST inform the user agent via the 201 (Created)
	 * response.
	 * If an existing resource is modified, either the 200 (OK) or
	 * 204 (No Content) response codes SHOULD be sent to indicate successful
	 * completion of the request. If the resource could not be created or
	 * modified with the Request-URI, an appropriate error response SHOULD
	 * be given that reflects the nature of the problem. The recipient of the
	 * entity MUST NOT ignore any Content-* (e.g. Content-Range) headers that it
	 * does not understand or implement and MUST return a 501 (Not Implemented)
	 * response in such cases.
	 *
	 * If the request passes through a cache and the Request-URI identifies one
	 * or more currently cached entities, those entries SHOULD be treated as
	 * stale. Responses to this method are not cacheable.
	 *
	 * The fundamental difference between the POST and PUT requests is reflected
	 * in the different meaning of the Request-URI. The URI in a POST request
	 * identifies the resource that will handle the enclosed entity. That
	 * resource might be a data-accepting process, a gateway to some other
	 * protocol, or a separate entity that accepts annotations. In contrast,
	 * the URI in a PUT request identifies the entity enclosed with the request
	 * -- the user agent knows what URI is intended and the server MUST NOT
	 * attempt to apply the request to some other resource.
	 * (source: http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.6)
	 *
	 * @todo implement
	 * @return void
	 * @param string $path
	 * @param array $params HTTP Params; Default: array()
	 */
	public function put($path, $format, array $params = array())
	{
		// If a new resource is created, the origin server MUST inform the user agent via the 201 (Created) response.
		// If an existing resource is modified, either the 200 (OK) or 204 (No Content) response codes SHOULD be sent to indicate successful completion of the request.
		$this->Response->end('HTTP PUT', 501); // 501 Not Implemented
	}

	/**
	 * HTTP request method DELETE.
	 *
	 * The DELETE method requests that the origin server delete the resource
	 * identified by the Request-URI. This method MAY be overridden by
	 * human intervention (or other means) on the origin server.
	 * The client cannot be guaranteed that the operation has been carried out,
	 * even if the status code returned from the origin server indicates that
	 * the action has been completed successfully.
	 * However, the server SHOULD NOT indicate success unless, at the time
	 * the response is given, it intends to delete the resource or move it to
	 * an inaccessible location.
	 *
	 * A successful response SHOULD be 200 (OK) if the response includes
	 * an entity describing the status, 202 (Accepted) if the action has not yet
	 * been enacted, or 204 (No Content) if the action has been enacted
	 * but the response does not include an entity.
	 * (source: http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.7)
	 *
	 * @todo implement
	 * @return void
	 * @param string $path
	 */
	public function delete($path)
	{
		$this->Response->end('HTTP DELETE', 501); // 501 Not Implemented
	}

	// /**
	//  * HTTP request method OPTIONS.
	//  *
	//  * @todo implement
	//  * @return void
	//  * @param string $path
	//  */
	// public function options($path)
	// {
	// 	$this->Response->end('HTTP OPTIONS', 501); // 501 Not Implemented
	// }

	/**
	 * This error handler will capture every exception once it is set by set_error_handler().
	 *
	 * @param integer $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param integer $errline
	 * @param array $errcontext
	 * @return boolean
	 */
	public function errorHandler($errno, $errstr, $errfile = __FILE__, $errline = __LINE__, array $errcontext = array())
	{
		$this->resetDom();
		$controller	= Module::createErrorController(new Exception($errstr, $errno, 1, $errfile, $errline));
		$controller->launch('get', $params = array(), $this->dom, $this);
		$View		= Module::getView();
		$this->statuscode	= 500; // Internal Server Error
		try
		{
			$this->Response->end($View->transform($this->dom)->to($this->Request->format), $this->statuscode);
			return true;
		}
		catch (\Exception $e)
		{
			$this->Response->Header->contentType('text/plain', 'UTF-8');
			$this->Response->end('Internal Server Error: '.$e, $this->statuscode);
			return false;
		}
	}

	/**
	 * This exception handler will capture every exception once it is set by set_exception_handler().
	 *
	 * @param \Exception $e
	 * @return boolean
	 */
	public function exceptionHandler(\Exception $e)
	{
		$this->resetDom();
		$controller	= Module::createErrorController($e);
		$controller->launch('get', $params = array(), $this->dom, $this);
		$View		= Module::getView();
		$this->statuscode	= 500; // Internal Server Error
		try
		{
			$this->Response->end($View->transform($this->dom)->to($this->Request->format), $this->statuscode);
			return true;
		}
		catch (\Exception $e)
		{
			$this->Response->end('Internal Server Error: '.$e, $this->statuscode);
			return false;
		}
	}

	/**
	 * Setter for stylesheets the current path ({@see getPath();}) is using.
	 *
	 * @param array $elements Default: array();
	 * @return array stylesheet array
	 */
	public static function stylesheets(array $elements = array())
	{
		return self::$stylesheets = array_merge(self::$stylesheets, $elements);
	}

	/**
	 * Setter for javscripts the current path ({@see getPath();}) is using.
	 *
	 * @param array $elements Default: array();
	 * @return array javascript array
	 */
	public static function javascripts(array $elements = array())
	{
		return self::$javascripts = array_merge(self::$javascripts, $elements);
	}

	/**
	 * Saves merges CSSs to the assetic build path.
	 *
	 * @return string Path to the built file.
	 */
	public static function dumpStylesheets()
	{
		$asset = self::getCssAssetFactory()->createAsset
		(	self::$stylesheets
		,	array // @todo make modifiable
			(	'less' // Less CSS Compiler
			,	'import' // Solves @imports
			// ,	'rewrite' // Rewrites Base URLs when moving to another URL
			,	'min' // Minifies the script
			)
		,	array('output' => 'assetic/*.css')
		);
		$cache = self::getAssetCache($asset);
		self::getAssetWriter()->writeAsset($cache);
		return self::basePath().'/'.$asset->getTargetPath();
	}

	/**
	 * Saves merges Javascripts to the assetic build path.
	 *
	 * @return string Path to the built file.
	 */
	public static function dumpJavascripts()
	{
		$asset = self::getJsAssetFactory()->createAsset
		(	self::$javascripts
		,	array(/* filters */) // @todo make modifiable
		,	array('output' => 'assetic/*.js')
		);
		$cache = self::getAssetCache($asset);
		self::getAssetWriter()->writeAsset($cache);
		return self::basePath().'/'.$asset->getTargetPath();
	}


	/**
	 * Creates an Assetic Asset Factory for all CSS files in this application.
	 *
	 * @return \Assetic\Factory\AssetFactory
	 */
	private static function getCssAssetFactory()
	{
		class_exists('\\CssMin') or require('lib/vendor/CssMin.php'); // @todo This class is very slow - it needs about 0.2 sec to load!!!!!!
		class_exists('\\lessc') or require('lib/vendor/lessphp/lessc.inc.php');

		$fm = new FilterManager();

		$fm->set('less', new Filter\LessPhpFilter());
		$fm->set('import', new Filter\CssImportFilter());
		$fm->set('rewrite', new Filter\CssRewriteFilter());
		$fm->set('min', new Filter\CssMinFilter());

		$factory = new AssetFactory(self::getAssetBuildPath());
		$factory->setFilterManager($fm);

		return $factory;
	}

	/**
	 * Creates an Assetic Asset Factory for all JS files in this application.
	 *
	 * @return \Assetic\Factory\AssetFactory
	 */
	private static function getJsAssetFactory()
	{
		$fm			= new FilterManager();
		$factory	= new AssetFactory(self::getAssetBuildPath());
		$factory->setFilterManager($fm);
		return $factory;
	}

	/**
	 * Creates an Assetic Asset Factory for all CSS files in this application.
	 *
	 * @return \Assetic\AssetWriter
	 */
	private static function getAssetWriter()
	{
		if (is_null(self::$AssetWriter))
		{
			self::$AssetWriter = new AssetWriter(self::getAssetBuildPath());
		}
		return self::$AssetWriter;
	}

	/**
	 * A decorator for assets which enables caching.
	 *
	 * @param \Assetic\Asset $asset
	 * @return \Assetic\Asset\AssetCache
	 */
	private static function getAssetCache($asset)
	{
		return new AssetCache
		(	$asset
		,	new FilesystemCache(self::getAssetBuildPath().'/cache')
		);
	}

	/**
	 * @return string Asset build path
	 * @todo make this modifiable by users. $this->assetBuildPath or something
	 */
	public static function getAssetBuildPath()
	{
		return dirname($_SERVER['SCRIPT_FILENAME']);
	}
}