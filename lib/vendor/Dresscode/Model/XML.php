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
 * @subpackage	Model
 */
namespace Dresscode\Model;

/**
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Dresscode
 * @subpackage	Model
 */
class XML extends \Dresscode\Model
{
	const XMLNS			= 'http://www.w3.org/XML/1998/namespace';
	const XINCLUDENS	= 'http://www.w3.org/2001/XInclude';
	const XREFNS		= 'x-schema:refSchema.xml';

	/**
	 * @var \DomDocument
	 */
	protected $dom;

	/**
	 * Unix timestamp
	 *
	 * @var integer
	 */
	protected $modified;

	/**
	 * @var array
	 */
	protected $fileList;


	/**
	 * @param \DirectoryIterator $resource
	 */
	public function __construct(\DirectoryIterator $resource)
	{
		if (!file_exists($file = $resource->getPath().DIRECTORY_SEPARATOR.'model.xml'))
		{
			throw new \Dresscode\Exception\FileNotFound($file);
		}
		$this->dom = new \DomDocument();
		$this->dom->preserveWhiteSpace = false;
		$this->dom->load($file);
		$this->modified = date('c', filemtime($file));
		$this->dom->xinclude();
	}

	/**
	 * @todo implement
	 */
	public function create($path, \DomNode $data)
	{
		return null;
	}

	/**
	 * @param string $path
	 * @return \DomElement
	 * @throws \Dresscode\Exception\FileNotFound
	 */
	public function read($path)
	{
		if ($node = $this->findMatch($path))
		{
			return $this->normalize($node);
		}
		throw new \Dresscode\Exception\FileNotFound($path);
	}

	/**
	 * @todo implement
	 */
	public function update($path, \DomNode $data)
	{
		return null;
	}

	/**
	 * @todo implement
	 */
	public function delete($path)
	{
		return null;
	}

	/**
	 * @param string $path
	 * @param string $language Default: null
	 * @return array
	 */
	public function keywords($path, $language = null)
	{
		$keywords = array();
		$xpath = new \DomXpath($this->dom);
		$xpath->registerNamespace('fs', self::NS);
		try
		{
			$file = $this->read($path);
			foreach ($xpath->query('.//fs:keywords/fs:element', $file) as $keyword)
			{
				if (!$language || !$keyword->getAttribute('lang') || strtolower($keyword->getAttribute('lang')) === strtolower($language))
				{
					$keywords[] = $keyword->nodeValue;
				}
			}
		}
		catch (\Exception $e)
		{}
		return $keywords;
	}

	/**
	 * list with direct descendants.
	 *
	 * @param string $path
	 * @return array
	 */
	public function getList($path)
	{
		if ($path === '/')
		{
			return $this->_buildFileList($this->dom->documentElement);
		}
		elseif ($matchingNode = $this->findMatch($path))
		{
			return $this->_buildFileList($matchingNode);
		}
		return array();
	}

	public function getDom()
	{
		return $this->dom;
	}

	/**
	 * Returns the timestamp of the $path or the XML file.
	 *
	 * @param string $path
	 * @return integer Unix timestamp
	 */
	public function lastModified($path = null)
	{
		if (is_null($path))
		{
			return $this->modified;
		}
		try
		{
			$date = $this->read($path)->getAttribute('modified') ?: $this->read($path)->getAttribute('created') ?: $this->modified;
			return date('c', strtotime($date));
		}
		catch (\Dresscode\Exception\FileNotFound $e)
		{
			return date('c', strtotime($this->modified));
		}
	}

	public function priority($path)
	{
		try
		{
			return $this->read($path)->getAttribute('priority') ?: null;
		}
		catch (\Dresscode\Exception\FileNotFound $e)
		{
			return null;
		}
	}

	/**
	 * Returns attributes named after the called $method.
	 *
	 * @param string $method
	 * @param array $arguments
	 * @return string
	 */
	public function __call($method, array $arguments)
	{
		try
		{
			if (isset($arguments[0]))
			{
				return $this->read($arguments[0])->getAttribute($method);
			}
			return $this->dom->documentElement->getAttribute($method);
		}
		catch (\Exception $e)
		{
			return null;
		}
	}

	/**
	 * @param string $path
	 * @return \DomNode
	 */
	protected function findMatch($path)
	{
		if ($path[0] !== '/')
		{
			throw new \Dresscode\Exception\NotAllowed('The model path must be absolute (start with a "/").');
		}
		foreach ($this->getFileList() as $pathCandidate => $node)
		{
			if (/* $node->hasChildNodes() && */ $pathCandidate === $path) // disabled hasChildNodes because of empty redirect files.
			{
				return $node;
			}
		}
		return null;
	}

	/**
	 * Returns the file list.
	 *
	 * Caches the result after the first call.
	 *
	 * @return array of \DomElement
	 */
	protected function getFileList()
	{
		if (is_null($this->fileList))
		{
			$this->fileList = $this->_buildFileListRecursive($this->dom->documentElement, false);
		}
		return $this->fileList;
	}

	/**
	 * @param \DomElement $element
	 * @param boolean $hideInvisibleFiles
	 * @return array
	 */
	protected function _buildFileList(\DomElement $element, $hideInvisibleFiles = true)
	{
		$list = array();
		foreach ($element->childNodes as $childNode)
		{
			if ($this->isValidFile($childNode) && (!$hideInvisibleFiles
				|| !$childNode->getAttribute('invisible')))
			{
				if ($token = $childNode->getAttribute('aliasToken'))
				{
					if ($translation = \Dresscode\Translator::translate($token))
					{
						$childNode->setAttribute('alias', $translation->nodeValue);
					}
					else
					{
						$childNode->setAttribute('alias', $token);
					}
				}
				$list[$this->getPath($childNode)] = $childNode;
			}
		}
		return $list;
	}

	/**
	 * @param \DomElement $element
	 * @param boolean $hideInvisibeFiles
	 * @return array of \DomElement
	 */
	protected function _buildFileListRecursive(\DomElement $element, $hideInvisibleFiles = true)
	{
		static $list = array();
		foreach ($this->_buildFileList($element, $hideInvisibleFiles) as $path => $fileNode)
		{
			$list[$path] = $fileNode;
			$this->_buildFileListRecursive($fileNode, $hideInvisibleFiles);
		}
		return $list;
	}

	protected function isValidFile(\DomNode $node)
	{
		return $node->localName === 'file'
				&& $node->lookupNamespaceURI($node->prefix ?: null) === self::NS;
	}

	/**
	 * @param \DomElement $node
	 * @return string regexp
	 */
	protected function getPath(\DomElement $node)
	{
		if ($node->localName === 'file')
		{
			$name	= '/'.$node->getAttribute('name');
			$parent	= $node;
			while (($parent = $parent->parentNode) && $parent->localName === 'file')
			{
				$name = '/'.$parent->getAttribute('name').$name;
			}
			return $name;
		}
	}

	/**
	 * Solve cross references.
	 *
	 * @param \DomElement $node
	 * @return \DomElement Same as the input node
	 */
	protected function normalize(\DomElement $node)
	{
		$xrefs = array();
		foreach ($node->getElementsByTagNameNS(self::XREFNS, '*') as $xref)
		{
			if ($element = $this->dom->getElementById($xref->getAttribute('ref')))
			{
				$clone = $element->cloneNode(true);
				$clone->removeAttributeNS(self::XMLNS, 'id');
			}
			else
			{
				// @todo create error node instead!
				throw new \Dresscode\Exception('Reference "'.$xref->getAttribute('ref').'" is not present (line '.$xref->getLineNo().')');
			}
			$xref->parentNode->insertBefore($this->normalize($clone), $xref);
			$xrefs[] = $xref;
		}
		foreach ($xrefs as $xref)
		{
			$xref->parentNode->removeChild($xref);
		}
		return $node;
	}
}