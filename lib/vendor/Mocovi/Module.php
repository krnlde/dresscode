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
 */
class Module
{
	/**
	 * @var \Mocovi\Pool
	 */
	protected static $Pool;

	/**
	 * @var \DirectoryIterator
	 */
	protected static $applicationPath;

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

	public static function initialize(\DirectoryIterator $applicationPath, \DirectoryIterator $commonPath, \DomDocument $dom)
	{
		self::$applicationPath		= $applicationPath;
		self::$commonPath			= $commonPath;
		self::$View					= new View\XSL(self::getCommonViewPath());
		self::$View->addPool(self::getViewPath());
		self::$Pool = new Pool('');
		self::$Pool->add(self::getCommonPath());
		self::$Pool->add(self::getPath());
		self::$dom = $dom;
	}

	/**
	 * @param \DomNode $sourceNode
	 * @return \Mocovi\Controller
	 */
	public static function createControllerFromNode(\DomNode $sourceNode)
	{
		$controller = self::_createControllerFromNode($sourceNode);
		return $controller;
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
	 * @param string $name Template name
	 * @return \DirectoryIterator template path
	 * @throws \Mocovi\Exception\ModuleNotFound
	 */
	public static function findTemplates($name)
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
	 * Returns the path of the current application.
	 *
	 * @return \DirectoryIterator
	 */
	public static function getPath()
	{
		return new \DirectoryIterator(self::$applicationPath->getPath().DIRECTORY_SEPARATOR.'modules');
	}

	/**
	 * Returns the common path for applications.
	 *
	 * @return \DirectoryIterator
	 */
	public static function getCommonPath()
	{
		return new \DirectoryIterator(self::$commonPath->getPath().DIRECTORY_SEPARATOR.'modules');
	}

	/**
	 * Returns the View path of the current application.
	 * @return \DirectoryIterator
	 */
	public static function getViewPath()
	{
		return new \DirectoryIterator(self::$applicationPath->getPath().DIRECTORY_SEPARATOR.'views');
	}

	/**
	 * Returns the common View path for applications.
	 *
	 * @return \DirectoryIterator
	 */
	public static function getCommonViewPath()
	{
		return new \DirectoryIterator(self::$commonPath->getPath().DIRECTORY_SEPARATOR.'views');
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
	protected static function _createControllerFromNode(\DomNode $sourceNode)
	{
		if ($sourceNode->nodeType === \XML_TEXT_NODE)
		{
			$moduleName = 'Inline';
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
			throw new Exception\ControllerNotFound($controllerPath, 0, 1, __FILE__, __LINE__, $e);
		}

		$controller = Controller::create($controllerPath, $sourceNode);

		try
		{
			$templatePath = $modulePath->getPath().DIRECTORY_SEPARATOR.'templates';

			if (file_exists($templatePath))
			{
				$templatePool = new \DirectoryIterator($templatePath);
				self::$View->addTemplatePool($templatePool, $sourceNode);
			}
		}
		catch (\UnexpectedValueException $e)
		{
			throw new Exception\TemplateNotFound($templatePath);
		}

		if ($sourceNode->hasChildNodes())
		{
			foreach ($sourceNode->childNodes as $childNode)
			{
				if (in_array($childNode->nodeType, array(XML_ELEMENT_NODE, XML_TEXT_NODE)))
				{
					if ($childController = self::_createControllerFromNode($childNode)) // Recursion
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
	public static function createNode($nodeName, $text = '', array $attributes = array())
	{
		$dom = self::$dom;
		$node = $dom->createElementNS(\Mocovi\Controller::NS, $nodeName, $text);
		foreach ($attributes as $key => $value)
		{
			$node->setAttribute($key, $value);
		}
		return $node;
	}

	/**
	 * @unused
	 */
	// public function createController($nodeName, $text = '', array $attributes = array())
	// {
	// 	$node		= Module::createNode($nodeName, $text, $attributes);
	// 	$controller	= Controller::create($node);
	// 	return $controller;
	// }

	/**
	 * @param \Exception $exception
	 * @return \DomNode
	 */
	public static function createErrorNode(\Exception $exception)
	{
		$error = self::createNode('error');
		$error->appendChild($headline = self::createNode('headline', get_class($exception)));
		$headline->setAttribute('priority', 2);
		$error->appendChild(self::createNode('cite', $exception->getMessage()));
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
		$controller	= self::createControllerFromNode($errorNode);
		return $controller;
	}
}