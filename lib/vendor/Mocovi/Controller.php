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
 * Abstract RESTful Controller.
 *
 * It uses an ReflectionObject of itself to determine its controller properties.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
abstract class Controller
{
	const NS = 'http://mocovi.de/schema/controller';

	/**
	 * @var \Mocovi\Application
	 */
	protected $Application;

	/**
	 * ReflectionObject is used to determine the type of the property in a controller.
	 *
	 * @var \ReflectionClass
	 */
	protected $Reflection;

	/**
	 * Array which contains tokenized doc comments (javadoc) from each property
	 * of this controller.
	 *
	 * @var array 2D Array
	 */
	protected $docCommentCache = array();

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
	 * CSS class or equivalent.
	 *
	 * @property
	 * @hideIfEmpty
	 * @var string
	 */
	protected $class;

	/**
	 * CSS ID or equivalent.
	 *
	 * @property
	 * @hideIfEmpty
	 * @var string
	 */
	protected $id;


	/**
	 * @param \DomNode $sourceNode
	 */
	final public function __construct(\DomNode $sourceNode)
	{

		$this->Reflection	= new \ReflectionClass($this);

		$this->sourceNode	= $sourceNode;

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
	final public static function create(\SplFileObject $controllerPath, \DomNode $sourceNode)
	{
		if ($sourceNode->nodeType !== \XML_TEXT_NODE)
		{
			if ($sourceNode->nodeType !== \XML_ELEMENT_NODE)
			{
				throw new Exception('Wrong node provided. Cannot extract a controller from this one "<'.$sourceNode->nodeName.'/>"). Node-Type musst be either "Text" or "Element"');
			}
			elseif ($sourceNode->lookupNamespaceURI($sourceNode->prefix ?: null) !== \Mocovi\Controller::NS) // $sourceNode->lookupNamespaceURI(null) returns the default namespace
			{
				throw new Exception('The namespace of the Controller "<'.$sourceNode->nodeName.'/>" must be "'.\Mocovi\Controller::NS.'"');
			}
		}

		if ($sourceNode->nodeType === \XML_TEXT_NODE)
		{
			$controllerName = 'Inline';
		}
		else
		{
			$controllerName = ucfirst($sourceNode->localName);
		}
		$class = '\Mocovi\Controller\\'.$controllerName;
		class_exists($class ,false)
			or require $controllerPath->getPathname();
		return new $class($sourceNode);
	}

	/**
	 * Startpoint for a controller.
	 *
	 * @param string $method HTTP method
	 * @param array $params array()
	 * @param \DomNode $parentNode
	 * @return \DomNode $destinationNode
	 */
	final public function launch($method, array $params = array(), \DomNode $parentNode, \Mocovi\Application $application)
	{
		if ($this->showtime())
		{
			try
			{
				$this->loadIn($parentNode)
					 ->setApplication($application)
					 ->before($params)
					 ;
				foreach ($this->children as $child)
				{
					if ($this->launchChild($child))
					{
						$child->launch($method, $params, $this->node, $application);
					}
				}
				$this->$method($params); // -> HTTP Method
				$this->after($params);
			}
			catch (\Exception $e)
			{
				$this->error($e);
			}
		}
		return $this->getNode();
	}

	/**
	 * @return boolean
	 */
	public function launchChild(\Mocovi\Controller $child)
	{
		return true;
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
	 */
	public function addChild(\Mocovi\Controller $child)
	{
		$this->children[] = $child;
		return $this;
	}

	/**
	 * @param \Mocovi\Controller $parent
	 * @return \Mocovi\Controller $this
	 */
	public function setParent(\Mocovi\Controller $parent)
	{
		$this->parent = $parent;
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return \Mocovi\Controller $this
	 */
	public function setProperty($name, $value)
	{
		if ($this->Reflection->hasProperty($name))
		{
			$property = $this->Reflection->getProperty($name);
			if ($this->isControllerProperty($property))
			{
				$type = $this->getPropertyType($property);
				$this->$name = $type ? $this->cast($value, $type) : $value;
				$this->removeFromDocCommentCache($name);
			}
			return $this;
		}
		// throw new \Exception($name.' is no property of '.get_class($this)); // Ignore undefined properties
	}

	/**
	 * Returns a specific property of the controller.
	 *
	 * @return mixed|null
	 */
	public function getPropery($name)
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
	 * @return array Controller Properties
	 */
	public function getProperties()
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
	 */
	public function adoptProperties()
	{
		if ($this->node->nodeType === XML_ELEMENT_NODE)
		{
			foreach ($this->getProperties() as $name => $value)
			{
				if (!is_null($value) && !$this->node->getAttribute($name))
				{
					$this->node->setAttribute($name, $value);
				}
			}
		}
		return $this;
	}

	/**
	 * Example:
	 * <code>
	 * 	<c:control>
	 * 		<c:param name="someName" value="someValue"/>
	 * 		<c:param name="someName" value="someOtherValue"/>
	 * 	</c:control>
	 * </code>
	 *
	 * @return array
	 * @todo implement
	 */
	// public function getParameters()
	// {
	// 	return null;
	// }

	/**
	 * @return string XQuery leading to the current sourcenode.
	 */
	public function getXQuery()
	{
		$xquery		= '';
		$node		= $this->sourceNode;
		$parentNode	= $node->parentNode;
		$dom		= $node->ownerDocument;
		do
		{
			$count = 1;
			foreach ($parentNode->childNodes as $childNode)
			{
				if ($childNode->nodeName === $node->nodeName)
				{
					if ($childNode === $node)
					{
						$xquery = '/'.$node->nodeName.'['.$count.']'.$xquery;
						continue;
					}
					else
					{
						$count++;
					}
				}
			}
			$node = $parentNode;
		}
		while (($parentNode = $parentNode->parentNode) && $node !== $dom);
		return $xquery;
	}

	/**
	 * Find child-Controllers.
	 *
	 * @param string $name Controller
	 * @return array Matching controllers
	 */
	public function find($name)
	{
		$matches = array();
		foreach ($this->children as $child)
		{
			if (strtolower($child->getName()) === strtolower($name))
			{
				$matches[] = $child;
			}
			$matches = array_merge($matches, $child->find($name)); // recursion!
		}
		return $matches;
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
	 */
	public function replaceNode(\DomNode $newNode)
	{
		if ($this->node instanceof \DomNode)
		{
			$this->node->parentNode->replaceChild
				( $newNode
				, $this->node
				);
		}
		elseif (empty($this->node) && $this->parentNode instanceof \DomElement)
		{
			$this->parentNode->appendChild($newNode);
		}
		else
		{
			throw new \Mocovi\Exception('Couldn\'t replace current node. Neither the current node nor the parentNode are instanceof DomNode or DomElement');
		}
		$this->node = $newNode;
		return $this;
	}

	/**
	 * @param string $nodeName
	 * @return \Mocovi\Controller $this
	 */
	public function renameNode($nodeName)
	{
		$newNode = $this->dom->createElement($nodeName);
		if ($this->node->attributes->length > 0)
		{
			foreach ($this->node->attributes as $attribute)
			{
				$newNode->setAttribute($attribute->name, $attribute->value);
			}
		}
		while ($this->node->firstChild)
		{
			$newNode->appendChild($this->node->firstChild);
		}
		$this->replaceNode($newNode);
		return $this;
	}

	/**
	 * @return \Mocovi\Controller $this
	 */
	public function deleteNode()
	{
		//$this->replaceNode($this->dom->createComment('deleted controller: '.$this->getName()));
		if ($this->parentNode instanceof \DomElement && $this->node instanceof \DomNode)
		{
			$this->parentNode->removeChild($this->node);
		}
		return $this;
	}

	/**
	 * @return \Mocovi\Controller $this
	 */
	public function error(\Exception $e)
	{
		$controller = \Mocovi\Module::createErrorController($e);
		$controller->launch('get', $params = array(), $this->node, $this->Application);
		$this->replaceNode($controller->getNode());
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
	 * @return \Mocovi\Controller
	 */
	final protected function loadIn(\DomNode $parentNode)
	{
		if (is_null($this->node))
		{
			$this->parentNode	= $parentNode;
			$this->dom			= $parentNode instanceof \DomDocument ? $parentNode : $parentNode->ownerDocument;
			$this->node			= $this->createNode();

			$this->parentNode->appendChild($this->node);
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
	{
	}

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
	 * @param array $params array()
	 * @return void
	 */
	protected function get(array $params = array())
	{
	}

	/**
	 * @param array $params array()
	 * @return void
	 */
	protected function post(array $params = array())
	{
	}

	/**
	 * @param array $params array()
	 * @return void
	 */
	protected function put(array $params = array())
	{
	}

	/**
	 * @param array $params array()
	 * @return void
	 */
	protected function delete(array $params = array())
	{
	}

	/**
	 * @param array $params array()
	 * @return void
	 */
	protected function options(array $params = array())
	{
	}


	/**
	 * Tells whether the property is a controller specific property or not.
	 *
	 * This is set by the "@property" DocComment.
	 *
	 * @param \ReflectionProperty $property
	 * @return boolean
	 */
	protected function isControllerProperty(\ReflectionProperty $property)
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
	protected function isHiddenProperty(\ReflectionProperty $property)
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
	protected function cast($value, $type)
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
				if ($value === 'false')
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
	protected function getPropertyType(\ReflectionProperty $property)
	{
		$tokens = $this->tokenizedDocComment($property);
		return isset($tokens['var']) ? $tokens['var'] : null;
	}

	/**
	 * @param string $docComment
	 * @return array
	 */
	protected function tokenizedDocComment(\ReflectionProperty $property)
	{
		if (isset($this->docCommentCache[$property->name]))
		{
			return $this->docCommentCache[$property->name];
		}
		$tokens = array();
		$docComment = trim(str_replace(array('/*', '*/', '*'), '', $property->getDocComment()));
		$lines = array_map(function ($element){
			return trim($element);
		}, explode(PHP_EOL, $docComment));
		foreach ($lines as $line)
		{
			$pieces = explode(' ', $line, 2); // @todo maybe use split('/\s+/'...) here?
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
	protected function unixtime($userdate)
	{
		if (empty($userdate))
		{
			return time(); //now
		}
		return strtotime($userdate);
	}
}