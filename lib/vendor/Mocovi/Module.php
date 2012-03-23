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
 * A "module" is a Controller combined with its contents (like XSL-Templates, Assets, etc.)
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 * @todo		Exclude the factory classes (createControllerFromNode, etc.)
 * @todo		Rename this class to something more intuitive. Suggestions?
 */
class Module
{
	/**
	 * @var \Mocovi\Pool
	 */
	protected static $Pool;

	protected static $Application;

	/**
	 * @var \DirectoryIterator
	 */
	protected static $commonPath;

	/**
	 * @var \Mocovi\View
	 */
	protected static $View;

	/**
	 * @var \DomDocument
	 */
	protected static $dom;

	public static function initialize(\Mocovi\Application $Application)
	{
		self::$Application			= $Application;
		self::$Pool					= new Pool('');
		self::$View					= new View\XSL(self::getCommonViewPath());
		self::$View->addPool(self::getViewPath());
		self::$Pool->add(self::getCommonPath());
		self::$Pool->add(self::getPath());
	}

	/**
	 * @param \DomNode $sourceNode
	 * @return \Mocovi\Controller
	 */
	public static function createControllerFromNode(\DomNode $sourceNode, \DomNode $parentNode)
	{
		return self::_createControllerFromNode($sourceNode, $parentNode);
	}

	/**
	 * Finds a module in the {@see $Pool} and returns the DirectoryIterator of the path.
	 *
	 * @param string $name Module name
	 * @return \DirectoryIterator module path.
	 * @throws \Mocovi\Exception\ModuleNotFound
	 */
	public static function find($name)
	{
		$name = ucfirst($name);
		if ($path = self::$Pool->find($name))
		{
			return new \DirectoryIterator($path);
		}
		throw new Exception\ModuleNotFound($name);
	}

	/**
	 * Finds a template inside a module (uses the {@see find();} method to find modules).
	 *
	 * @param string $name Module name
	 * @return \DirectoryIterator template path
	 * @throws \Mocovi\Exception\ModuleNotFound
	 */
	protected static function findTemplates($name)
	{
		if ($module = self::find($name))
		{
			if (file_exists($templatePath = $module->getPath().DIRECTORY_SEPARATOR.'templates'))
			{
				return new \DirectoryIterator($templatePath);
			}
		}
	}

	/**
	 * Finds a controller inside a module (uses the {@see find();} method to find modules).
	 *
	 * @param string $name Module name
	 * @return \DirectoryIterator controller path
	 * @throws \Mocovi\Exception\ModuleNotFound
	 */
	public static function findController($name)
	{
		if ($module = self::find($name))
		{
			return new \SplFileObject($module->getPath().DIRECTORY_SEPARATOR.'controller.php');
		}
	}

	/**
	 * Finds a controller inside a module (uses the {@see find();} method to find modules).
	 *
	 * @param string $name Module name
	 * @return void
	 * @throws \Mocovi\Exception\ModuleNotFound
	 */
	public static function requireController($name)
	{
		require_once(self::findController($name)->getRealPath());
	}

	/**
	 * Returns the path of the current application.
	 *
	 * @return \DirectoryIterator
	 */
	public static function getPath()
	{
		return new \DirectoryIterator(self::$Application->getPath()->getPath().DIRECTORY_SEPARATOR.'modules');
	}

	/**
	 * Returns the common path for applications.
	 *
	 * @return \DirectoryIterator
	 */
	public static function getCommonPath()
	{
		return new \DirectoryIterator(self::$Application->getCommonPath()->getPath().DIRECTORY_SEPARATOR.'modules');
	}

	/**
	 * Returns the View path of the current application.
	 * @return \DirectoryIterator
	 */
	public static function getViewPath()
	{
		return new \DirectoryIterator(self::$Application->getPath()->getPath().DIRECTORY_SEPARATOR.'views');
	}

	/**
	 * Returns the common View path for applications.
	 *
	 * @return \DirectoryIterator
	 */
	public static function getCommonViewPath()
	{
		return new \DirectoryIterator(self::$Application->getCommonPath()->getPath().DIRECTORY_SEPARATOR.'views');
	}

	/**
	 * @return \Mocovi\View
	 */
	public static function getView()
	{
		return self::$View;
	}

	/**
	 * Creates a controller hierarchically based on its source node.
	 *
	 * This method is called recursively!
	 *
	 * @param \DomNode $sourceNode
	 * @return \Mocovi\Controller
	 * @throws \Mocovi\Exception\ControllerNotFound, \Mocovi\Exception\TemplateNotFound
	 */
	protected static function _createControllerFromNode(\DomNode $sourceNode, \DomNode $parentNode)
	{
		if ($sourceNode->nodeType === \XML_TEXT_NODE)
		{
			$moduleName = 'Plain';
		}
		elseif ($sourceNode->lookupNamespaceURI($sourceNode->prefix ?: null) !== \Mocovi\Controller::NS) // ignore other nodes
		{
			return null;
		}
		else
		{
			$moduleName = $sourceNode->localName;
		}

		$modulePath = self::find($moduleName);
		try
		{
			$controllerPath = new \SplFileObject($controllerPath = $modulePath->getPath().DIRECTORY_SEPARATOR.'controller.php');
		}
		catch (\RuntimeException $e)
		{
			throw new Exception\ControllerNotFound($controllerPath, null, null, null, null, $e);
		}

		$controller = Controller::create($controllerPath, $sourceNode, $parentNode, self::$Application);

		if ($templatePool = self::findTemplates($moduleName))
		{
			self::$View->addTemplatePool($templatePool);
		}
		else
		{
			// throw new Exception\TemplateNotFound($moduleName); // silently accept the missing template
		}

		if ($sourceNode->hasChildNodes())
		{
			foreach ($sourceNode->childNodes as $childNode)
			{
				if (in_array($childNode->nodeType, array(XML_ELEMENT_NODE, XML_TEXT_NODE)))
				{
					if ($childController = self::_createControllerFromNode($childNode, $controller->getNode())) // Recursion
					{
						$childController->setParent($controller);
						$controller->addChild($childController);
					}
				}
			}
		}
		return $controller;
	}

	/**
	 * @param string $nodeName
	 * @param string $text
	 * @param array $attributes array();
	 * @return \DomNode
	 */
	public static function createNode($nodeName, $text = null, array $attributes = array())
	{
		if ($templatePool = self::findTemplates($nodeName))
		{
			self::$View->addTemplatePool($templatePool);
		}
		$node = self::$Application->getDom()->createElementNS(\Mocovi\Controller::NS, $nodeName, $text);
		foreach ($attributes as $key => $value)
		{
			$node->setAttribute($key, $value);
		}
		return $node;
	}

	/**
	 * @param string $nodeName
	 * @param string $text Default: null
	 * @param array $attributes Default: array();
	 * @return \Mocovi\Controller
	 */
	public static function createController($nodeName, $text = null, array $attributes = array())
	{
		$node = Module::createNode($nodeName, $text, $attributes);
		return self::createControllerFromNode($node);
	}

	/**
	 * @param \Exception $exception
	 * @return \DomNode
	 * @todo Make this method prettier. Use createController() and stuff
	 */
	public static function createErrorNode(\Exception $exception)
	{
		// echo $exception; // @debug
		$error = self::createNode('error');
		$error->appendChild($headline = self::createNode('headline', get_class($exception)));
		$headline->setAttribute('priority', 2);
		$error->appendChild(self::createNode('quote', $exception->getMessage()));
		$error->appendChild(self::createNode('paragraph', ' thrown in '.$exception->getFile().' on line '.$exception->getLine()));
		$error->appendChild($stacktraceHeadline = self::createNode('headline', 'Stacktrace:'));
		$stacktraceHeadline->setAttribute('priority', 2);
		$error->appendChild($listing = self::createNode('listing'));
		$listing->setAttribute('type', 'ordered');
		foreach ($exception->getTrace() as $trace)
		{
			$listing->appendChild(self::createNode
				(	'paragraph'
				,	(isset($trace['class']) ? $trace['class'] : 'unknown')
				.	(isset($trace['type']) ? $trace['type'] : '')
				.	(isset($trace['function']) ? $trace['function'] : 'unknown')
				.	'('
				.	(isset($trace['args']) ? implode(', ', array_map(function($element) {
						switch (gettype($element))
						{
							case 'object':
								return get_class($element);
							break;
							case 'array':
								return 'Array';
							break;
							default:
								return '"'.$element.'"';
						}
					}, $trace['args'])) : '')
				.	') in '
				.	(isset($trace['file']) ? $trace['file'] : '')
				.	':'
				.	(isset($trace['line']) ? $trace['line'] : 0)
				)
			);
		}
		return $error;
	}

	/**
	 * @param \Exception $exception
	 * @return \Mocovi\Controller
	 */
	public static function createErrorController(\Exception $exception)
	{
		$errorNode	= self::createErrorNode($exception);
		$controller	= self::createControllerFromNode($errorNode, self::$Application->getDom());
		return $controller;
	}
}