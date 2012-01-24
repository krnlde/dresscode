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
 * @subpackage	View
 */
namespace Mocovi\View;

/**
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 * @subpackage	View
 */
class XSL extends \Mocovi\View
{

	/**
	 * @var \XSLTProcessor
	 */
	protected $Xslt;

	/**
	 * @param \DirectoryIterator $resource
	 */
	public function __construct(\DirectoryIterator $resource)
	{
		parent::__construct();
		$this->addPool($resource);
	}

	/**
	 * @param \DomDocument $dom
	 * @return \Mocovi\View\Intermediate
	 */
	public function transform(\DomDocument $dom)
	{
		$this->Xslt = new \XSLTProcessor();
		$this->Xslt->registerPHPFunctions(); // Allows the XSL to access custom PHP functions. php:function('name', ['param1', ['param2', ...]]);
		return new Intermediate($this, $dom);
	}

	/**
	 * @param string format
	 * @return \Mocovi\View\XSL $this
	 */
	public function setOutputFormat($format)
	{
		if(!($path = $this->Pool->find($format)))
		{
			throw new \Mocovi\Exception\WrongFormat($format);
		}
		$Xsl						= new \DomDocument();
		$Xsl->preserveWhiteSpace	= false;
		$Xsl->formatOutput			= false;
		$Xsl->load($path);
		$ns = $Xsl->lookupNamespaceURI('xsl');
		foreach($this->templates as $template)
		{
			$Pool = new \Mocovi\Pool('xsl');
			$Pool->add($template);
			if($path = $Pool->find($format))
			{
				$Xsl->documentElement->insertBefore($import = $Xsl->createElementNS($ns, 'import'), $Xsl->documentElement->firstChild);
				$import->setAttribute('href', str_replace('\\', '/', $path)); // important! without replacing backslashes it won't work on windows machines!
			}
		}
		// die($Xsl->saveXML()); // @debug
		$this->Xslt->importStyleSheet($Xsl);
		return $this;
	}

	/**
	 * This method performs the actual transformation from the parameter DomDocument to the {@see setOutputFormat()}.
	 *
	 * @param \DomDocument $Dom
	 * @return string
	 */
	public function _transform(\DomDocument $Dom)
	{
		return $this->Xslt->transformToXml($Dom);
	}
}