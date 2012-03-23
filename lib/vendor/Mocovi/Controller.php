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

use \Mocovi\Event;

/**
 * Abstract RESTful Controller.
 *
 * It uses an ReflectionObject of itself to determine its controller properties
 * set by the "@property" DocComment. By setting the "@hidden" comment the property
 * won't be set as attribute from the output node.
 *
 * TL;DR
 *
 * * @property - adopt value from the source node and send it attribute to the output node
 * * @hidden - prevent adoption for the output node but adopt from the sourceNode
 * * @hideIfEmpty - same as @hidden but only if empty
 * * @var [type] - enables type casting for the property (boolean, integer, float, string, character)
 * * @pattern [perl regexp] - accepts new input only when matching the pattern
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
abstract class Controller extends Observable
{
	const NS = 'http://mocovi.de/schema/controller';

	/**
	 * @var \Mocovi\Application
	 */
	protected $Application;

	/**
	 * @var \DomNode
	 */
	protected $sourceNode;

	/**
	 * @var \DomNode
	 */
	protected $parentNode;

	/**
	 * @var \DomNode
	 */
	protected $node;

	/**
	 * Destination OwnerDocument
	 *
	 * @var \DomDocument
	 */
	protected $dom;

	/**
	 * The first controller has no parent.
	 *
	 * @var \Mocovi\Controller
	 */
	protected $parent = null;

	/**
	 * @var array of \Mocovi\Controller
	 */
	protected $children = array();


	/* Controller Properties */

	/**
	 * @property
	 * @hidden
	 * @var string
	 */
	protected $showFromDate;

	/**
	 * @property
	 * @hidden
	 * @var string
	 */
	protected $showToDate;

	/**
	 * @property
	 * @var string
	 */
	protected $class;

	/**
	 * @property
	 * @var string
	 */
	protected $id;


	/**
	 * ReflectionObject is used to determine the type of the property in a controller.
	 *
	 * @var \ReflectionClass
	 */
	private $Reflection;

	/**
	 * Array which contains tokenized doc comments (javadoc) from each property
	 * of this controller.
	 *
	 * @var array 2D Array
	 */
	private $docCommentCache = array();

	/**
	 * Tells whether the properties have already been adopted or not.
	 *
	 * @var boolean
	 */
	private $propertiesAdopted = false;

	/**
	 * @param \DomNode $sourceNode
	 */
	final public function __construct(\DomNode $sourceNode, $parentNode, $Application)
	{
		$this->Reflection	= new \ReflectionClass($this);
		$this->sourceNode	= $sourceNode;
		$this->loadIn($parentNode);
		$this->setApplication($Application);
		if ($this->sourceNode->hasAttributes())
		{
			foreach ($this->sourceNode->attributes as $attribute)
			{
				$this->setProperty($attribute->name, $attribute->value);
			}
		}
	}

	/**
	 * Controller Factory.
	 *
	 * @param \SplFileObject $controllerPath
	 * @param \DomElement $sourceNode
	 * @param \DomElement $destinationNode
	 * @return \Mocovi\Controller
	 */
	final public static function create(\SplFileObject $controllerPath, \DomNode $sourceNode, \DomNode $parentNode, \Mocovi\Application $Application)
	{
		if ($sourceNode->nodeType !== \XML_TEXT_NODE)
		{
			if ($sourceNode->nodeType !== \XML_ELEMENT_NODE)
			{
				throw new \Mocovi\Exception('Wrong node provided. Cannot extract a controller from this one "<'.$sourceNode->nodeName.'/>"). Node-Type musst be either "Text" or "Element"');
			}
			elseif ($sourceNode->lookupNamespaceURI($sourceNode->prefix ?: null) !== \Mocovi\Controller::NS) // $sourceNode->lookupNamespaceURI(null) returns the default namespace
			{
				throw new \Mocovi\Exception('The namespace of the Controller "<'.$sourceNode->nodeName.'/>" must be "'.\Mocovi\Controller::NS.'"');
			}
		}

		if ($sourceNode->nodeType === \XML_TEXT_NODE)
		{
			$controllerName = 'Plain';
		}
		else
		{
			$controllerName = ucfirst($sourceNode->localName);
		}
		$class = '\\Mocovi\\Controller\\'.$controllerName;
		require_once($controllerPath->getPathname());
		return new $class($sourceNode, $parentNode, $Application);
	}

	/**
	 * Startpoint for a controller.
	 *
	 * @param string $method HTTP method
	 * @param array $params array()
	 * @param \DomNode $parentNode
	 * @return void
	 * @triggers launchChild
	 * @triggers ready
	 */
	final public function launch($method, array $params = array())
	{
		if ($this->showtime())
		{
			try
			{
				$this->before($params);
				foreach ($this->children as $child)
				{
					if (!$this->trigger('launchChild', $child)->isDefaultPrevented())
					{
						$child->launch($method, $params, $this->node);
					}
				}
				$this->$method($params); // -> HTTP Method
				$this->after($params);
			}
			catch (\Mocovi\Exception\Input $e)
			{
				throw $e;
			}
			catch (\Exception $e)
			{
				$this->error($e);
			}
		}
		$this->trigger('ready');
	}

	public function __clone()
	{
		$children		= array();
		$this->node		= $this->node->cloneNode(false);
		foreach ($this->children as $child)
		{
			$children[] = $newChild = clone $child;
			$newChild->setParent($this);
		}
		$this->children	= $children;
	}

	/**
	 * Returns the directory contain this controller.
	 *
	 * @return \DirectoryIterator
	 */
	public function getPath()
	{
		return \Mocovi\Module::find($this->getName());
	}

	// @todo Callbacks as middleware like in http://expressjs.com/guide.html#route-middleware

	/**
	 * @return \DomDocument
	 */
	public function getDom()
	{
		return $this->dom;
	}

	/**
	 * @return \DomElement
	 */
	public function getNode()
	{
		return $this->node;
	}

	/**
	 * @return \Mocovi\Controller
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @return Controller name
	 */
	public function getName()
	{
		$pieces = explode('\\', get_class($this));
		return $pieces[count($pieces) - 1];
	}

	/**
	 * @return boolean
	 */
	public function hasChildren()
	{
		return (count($this->children) > 0);
	}

	/**
	 * @param \Mocovi\Controller $child
	 * @return \Mocovi\Controller $this
	 * @triggers addChild
	 */
	public function addChild(\Mocovi\Controller $child)
	{
		if (!$this->trigger('addChild', $child)->isDefaultPrevented())
		{
			$this->children[] = $child;
			$child->setParent($this);
		}
		return $this;
	}

	/**
	 * @param \Mocovi\Controller $parent
	 * @return \Mocovi\Controller $this
	 * @triggers setParent
	 */
	public function setParent(\Mocovi\Controller $parent)
	{
		if (!$this->trigger('setParent', $parent)->isDefaultPrevented())
		{
			$this->parent = $parent;
			$this->parentNode = $parent->getNode();
			$this->parentNode->appendChild($this->node);
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return \Mocovi\Controller $this
	 * @triggers setProperty
	 */
	final public function setProperty($name, $value)
	{
		if ($this->Reflection->hasProperty($name))
		{
			$property = $this->Reflection->getProperty($name);
			if ($this->isControllerProperty($property))
			{
				if (!$this->trigger('setProperty', $name, array('value' => $value))->isDefaultPrevented())
				{
					$pattern = $this->getPropertyPattern($property);
					if (!$pattern || ($match = preg_match($pattern, $value)))
					{
						$type = $this->getPropertyType($property);
						$this->$name = $type ? $this->cast($value, $type) : $value;
						if ($this->propertiesAdopted)
						{
							$this->node->setAttribute($name, $this->$name);
						}
						$this->removeFromDocCommentCache($name);
					}
					elseif ($pattern)
					{
						throw new \Mocovi\Exception\WrongFormat($name);
					}
				}
			}
			return $this;
		}
		// @strict
		// throw new \Exception($name.' is no property of '.get_class($this)); // Ignore undefined properties
	}

	/**
	 * Returns a specific property of this controller.
	 *
	 * @param string $name
	 * @return mixed|null
	 */
	final public function getProperty($name)
	{
		if ($this->Reflection->hasProperty($name))
		{
			$property = $this->Reflection->getProperty($name);
			if ($this->isControllerProperty($property) && !$this->isHiddenProperty($property))
			{
				return $this->$name;
			}
		}
		return null;
	}

	/**
	 * Returns all properties of this controller.
	 *
	 * @return array Controller Properties
	 */
	final public function getProperties()
	{
		$properties = array();
		foreach ($this->Reflection->getProperties() as $property)
		{
			if ($this->isControllerProperty($property) && !$this->isHiddenProperty($property))
			{
				$name = $property->name;
				$properties[$name] = $this->$name;
			}
		}
		return $properties;
	}

	/**
	 * The destination node will get all attributes from the source node + default properties of the controller.
	 *
	 * @return \Mocovi\Controller $this
	 * @triggers adoptProperty
	 */
	public function adoptProperties()
	{
		if ($this->node->nodeType === XML_ELEMENT_NODE)
		{
			foreach ($this->getProperties() as $name => $value)
			{
				if (!is_null($value) && !$this->node->hasAttribute($name))
				{
					if (!$this->trigger('adoptProperty', $name, array('value' => $value))->isDefaultPrevented())
					{
						$this->node->setAttribute($name, $value);
					}
				}
			}
		}
		$this->propertiesAdopted = true;
		return $this;
	}

	/**
	 * @return string XPath leading to the current {@see $sourceNode}.
	 */
	public function getXPath()
	{
		return $this->sourceNode->getNodePath();
	}

	/**
	 * Find child controllers.
	 *
	 * @param string $name Searched Wanted child controller
	 * @return \Mocovi\Controller\Collection Matching controllers
	 */
	public function find($name)
	{
		return new Controller\Collection($this->_find($name));
	}

	protected function _find($name)
	{
		$matches = array();
		foreach ($this->children as $child)
		{
			if (strtolower($child->getName()) === strtolower($name))
			{
				$matches[] = $child;
			}
			$matches = array_merge($matches, $child->_find($name)); // recursion!
		}
		return $matches;
	}

	/**
	 * Find closest parent controller.
	 *
	 * @param string $name Wanted parent controller
	 * @return \Mocovi\Controller\Collection Matching controllers
	 */
	public function closest($name)
	{
		$parent = $this;
		while ($parent = $parent->getParent())
		{
			if (strtolower($parent->getName()) === strtolower($name))
			{
				return new Controller\Collection(array($parent));
			}
		}
		return new Controller\Collection();
	}

	/**
	 * Returns the root controller
	 *
	 * @unused
	 * @return \Mocovi\Controller
	 */
	// public function getRootParent()
	// {
	// 	$root = $this;
	// 	while ($r = $root->getParent())
	// 	{
	// 		$root = $r;
	// 	}
	// 	return $root;
	// }

	/**
	 * Removes a property from the {@see $docCommentCache}.
	 *
	 * @param $string name Property cached in {@see $docCommentCache}
	 * @return \Mocovi\Controller $this
	 */
	public function removeFromDocCommentCache($name)
	{
		if (isset($this->docCommentCache[$name]))
		{
			unset($this->docCommentCache[$name]);
		}
		return $this;
	}

	/**
	 * Replaces this control node with a new DomNode.
	 *
	 * @param \DomNode $newNode
	 * @return \Mocovi\Controller $this
	 * @triggers replaceNode
	 */
	public function replaceNode(\DomNode $newNode)
	{
		if (!$this->trigger('replaceNode', $newNode)->isDefaultPrevented())
		{
			if ($this->node instanceof \DomNode)
			{
				try
				{
					$this->parentNode->replaceChild
						( $newNode
						, $this->node
						);
				}
				catch (\Exception $e) // NotFoundException or similar
				{
					if (!$newNode->ownerDocument->isSameNode($this->dom))
					{
						$newNode = $this->dom->importNode($newNode, true);
					}
					$this->parentNode->appendChild($newNode);
				}
			}
			elseif (empty($this->node) && $this->parentNode instanceof \DomElement)
			{
				if (!$newNode->ownerDocument->isSameNode($this->dom))
				{
					$newNode = $this->dom->importNode($newNode, true);
				}
				$this->parentNode->appendChild($newNode);
			}
			else
			{
				throw new \Mocovi\Exception('Couldn\'t replace current node. Neither the current node nor the parentNode are instanceof DomNode or DomElement');
			}
			$this->node = $newNode;
		}
		return $this;
	}

	/**
	 * @param string $nodeName
	 * @return \Mocovi\Controller $this
	 * @triggers renameNode
	 */
	public function renameNode($nodeName)
	{
		if (!$this->trigger('renameNode', $nodeName)->isDefaultPrevented())
		{
			$newNode = $this->dom->createElement($nodeName);
			foreach ($this->node->attributes as $attribute)
			{
				$newNode->setAttribute($attribute->name, $attribute->value);
			}
			while ($this->node->firstChild)
			{
				$newNode->appendChild($this->node->firstChild);
			}
			$this->replaceNode($newNode);
		}
		return $this;
	}

	/**
	 * @return \Mocovi\Controller $this
	 * @triggers deleteNode
	 */
	public function deleteNode()
	{
		//$this->replaceNode($this->dom->createComment('deleted controller: '.$this->getName()));
		if (!$this->trigger('deleteNode')->isDefaultPrevented())
		{
			if ($this->parentNode instanceof \DomElement && $this->node instanceof \DomNode)
			{
				$this->parentNode->removeChild($this->node);
			}
		}
		return $this;
	}

	/**
	 * Generates a informative node out of the Exception provided as argument.
	 *
	 * @param \Exception $e
	 * @return \Mocovi\Controller $this
	 * @triggers error
	 */
	public function error(\Exception $e)
	{
		if (!$this->trigger('error', $e)->isDefaultPrevented())
		{
			$controller = \Mocovi\Module::createErrorController($e);
			$controller->launch('get', $params = array(), $this->node, $this->Application);
			$this->replaceNode($controller->getNode());
		}
		return $this;
	}

	/**
	 * Tells if this program is in time and will be executed.
	 *
	 * If the current time is in the range set in the model XML, the program
	 * will be executed.
	 *
	 * @return boolean Tells if it's "showtime" for this control.
	 */
	public function showtime()
	{
		$from	= $this->unixtime($this->showFromDate);
		$to		= $this->unixtime
			( str_replace
				( '*'
				, date('r', $from)
				, $this->showToDate
				)
			);
		return ($from <= ($now = time()) && $now <= $to);
	}



	/**
	 * Initializes the loading of the own outout node.
	 *
	 * @param \DomNode $parentNode
	 * @return \Mocovi\Controller $this
	 */
	final protected function loadIn(\DomNode $parentNode)
	{
		if (is_null($this->node))
		{
			$this->parentNode	= $parentNode;
			$this->dom			= $parentNode instanceof \DomDocument ? $parentNode : $parentNode->ownerDocument;
			$this->node			= $this->createNode();

			if ($this->node instanceof \DomNode && $this->node->ownerDocument->isSameNode($this->dom))
			{
				$this->parentNode->appendChild($this->node);
			}
		}
		return $this;
	}

	/**
	 * Sets the current application to have access of all environment features (like headers).
	 *
	 * @param \Mocovi\Application $application
	 * @return \Mocovi\Controller $this
	 */
	final protected function setApplication(\Mocovi\Application $application)
	{
		if (is_null($this->Application))
		{
			$this->Application = $application;
		}
		return $this;
	}


	/**
	 * This method will be executed before the requested method.
	 *
	 * Can be overriden whenever needed.
	 *
	 * @param array $params array()
	 * @return void
	 */
	protected function before(array $params = array())
	{}

	/**
	 * This method will be executed after the requested method.
	 *
	 * Can be overriden whenever needed.
	 *
	 * @param array $params array()
	 * @return void
	 */
	protected function after(array $params = array())
	{
		$this->adoptProperties();
	}

	/**
	 * Default method to create the output node.
	 *
	 * Can be overriden whenever needed.
	 *
	 * @return \DomNode
	 */
	protected function createNode()
	{
		return $this->dom->createElement($this->sourceNode->localName);
	}

	/**
	 * Represents the HTTP GET method.
	 *
	 * Can be implemented in deriving controllers.
	 *
	 * @param array $params array()
	 * @return void
	 */
	protected function get(array $params = array())
	{}

	/**
	 * Represents the HTTP POST method.
	 *
	 * Can be implemented in deriving controllers.
	 *
	 * @param array $params array()
	 * @return void
	 */
	protected function post(array $params = array())
	{}

	/**
	 * Represents the HTTP PUT method.
	 *
	 * Can be implemented in deriving controllers.
	 *
	 * @param array $params array()
	 * @return void
	 */
	protected function put(array $params = array())
	{}

	/**
	 * Represents the HTTP DELETE method.
	 *
	 * Can be implemented in deriving controllers.
	 *
	 * @param array $params array()
	 * @return void
	 */
	protected function delete(array $params = array())
	{}

	/**
	 * Represents the HTTP OPTIONS method.
	 *
	 * Can be implemented in deriving controllers.
	 *
	 * @param array $params array()
	 * @return void
	 */
	protected function options(array $params = array())
	{}


	/**
	 * Tells whether the property is a controller specific property or not.
	 *
	 * This is set by the "@property" DocComment.
	 *
	 * @param \ReflectionProperty $property
	 * @return boolean
	 */
	final protected function isControllerProperty(\ReflectionProperty $property)
	{
		$tokens = $this->tokenizedDocComment($property);
		return isset($tokens['property']);
	}

	/**
	 * Tells whether the property is hidden.
	 *
	 * This is set by the "@hidden" and "@hideIfEmpty" DocComments.
	 *
	 * @param \ReflectionProperty $property
	 * @return boolean
	 */
	final protected function isHiddenProperty(\ReflectionProperty $property)
	{
		$tokens = $this->tokenizedDocComment($property);
		return isset($tokens['hidden']) || (isset($tokens['hideifempty']) && empty($this->{$property->name}));
	}

	/**
	 * Casts the value by its type.
	 *
	 * This is done via the PHP internal typecasting.
	 *
	 * @param string $value
	 * @param string $type
	 * @return mixed
	 */
	protected static function cast($value, $type)
	{
		switch (strtolower($type))
		{
			case 'int':
				//fall through
			case 'integer':
				return (int) $value;
			break;
			case 'float':
				//fall through
			case 'double':
				return (float) $value;
			break;
			case 'bool':
				//fall through
			case 'boolean':
				if (in_array(strtolower($value), array('false', 'no')))
				{
					return false;
				}
				return (bool) $value;
			break;
			case 'string':
				return (string) $value;
			break;
			case 'char':
				//fall through
			case 'character':
				return (string) substr($value, 0, 1);
			break;
			default:
				return $value;
		}
	}

	/**
	 * @param \ReflectionProperty $property
	 * @return string|null
	 */
	final protected function getPropertyType(\ReflectionProperty $property)
	{
		$tokens = $this->tokenizedDocComment($property);
		return isset($tokens['var']) ? $tokens['var'] : null;
	}

	/**
	 * @param \ReflectionProperty $property
	 * @return string|null
	 */
	final protected function getPropertyPattern(\ReflectionProperty $property)
	{
		$tokens = $this->tokenizedDocComment($property);
		return isset($tokens['pattern']) ? $tokens['pattern'] : null;
	}

	/**
	 * @param \ReflectionProperty $property
	 * @return array
	 */
	final protected function tokenizedDocComment(\ReflectionProperty $property)
	{
		if (isset($this->docCommentCache[$property->name]))
		{
			return $this->docCommentCache[$property->name];
		}
		$tokens		= array();
		$docComment	= trim(str_replace(array('/*', '*/', '*'), '', $property->getDocComment()));
		$lines		= array_map
		(	function ($element)
			{
				return trim($element);
			}
		,	preg_split("/[\r\n]+/", $docComment)
		);
		foreach ($lines as $line)
		{
			// $pieces = explode(' ', $line, 2); // this is faster than preg_split
			$pieces = preg_split('/\s+/', $line, 2);
			if (isset($pieces[0][0]) && $pieces[0][0] == '@')
			{
				$tokens[strtolower(substr($pieces[0], 1))] = isset($pieces[1]) ? $pieces[1] : true;
			}
		}
		$this->docCommentCache[$property->name] = $tokens;
		return $tokens;
	}

	/**
	 * Converts a datestring in unix timestamp.
	 *
	 * http://www.gnu.org/software/tar/manual/html_chapter/Date-input-formats.html#SEC115
	 *
	 * @param string $userdate Contains the the user time format
	 * @return integer unix timestamp; Returns either the converted time or the current time
	 */
	final protected static function unixtime($userdate)
	{
		if (empty($userdate))
		{
			return time(); //now
		}
		return strtotime($userdate);
	}

	/**
	 *
	 * @return string First 8 chars from the sha1 of the XPath
	 */
	protected function generateId()
	{
		return substr(sha1($this->getXPath()), 0, 7);
	}
}
