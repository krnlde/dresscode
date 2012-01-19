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

class_exists('Mocovi\\View\\Intermediate') or require('View/Intermediate.php');

/**
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
abstract class View
{
	/**
	 * Array-Pool of usable templates.
	 *
	 * @var array
	 */
	protected $templates = array();

	/**
	 * Pool for Views.
	 *
	 * @var \Mocovi\Pool
	 */
	protected $Pool;


	public function __construct()
	{
		$this->Pool = new \Mocovi\Pool('xsl');
	}

	/**
	 * The returning Intermediate is just syntactical sugar for a fluent interface.
	 *
	 * This method just exist for readability reasons.
	 * It is easier to read this:
	 * <code>
	 * 	$View->transform($dom)->to($format);
	 * </code>
	 * than that:
	 * <code>
	 * 	$View->setOutputFormat($format)->_transform($dom);
	 * </code>
	 *
	 * @param \DomDocument $dom
	 * @return \Mocovi\View\Intermediate
	 */
	abstract public function transform(\DomDocument $Dom);

	/**
	 * Sets the desired output format.
	 *
	 * @param string $format
	 * @return \Mocovi\View $this
	 */
	abstract public function setOutputFormat($format);

	/**
	 * Performs the actual transformation based on the output format.
	 *
	 * @param \DomDocument $Dom
	 * @return string transformed output
	 */
	abstract public function _transform(\DomDocument $Dom);

	/**
	 * Adds a view pool.
	 *
	 * @param \DirectoryIterator $resource
	 * @return \Mocovi\View $this
	 */
	public function addPool(\DirectoryIterator $resource)
	{
		$this->Pool->add($resource);
		return $this;
	}

	/**
	 * Adds a template pool.
	 *
	 * @param \DirectoryIterator $resource
	 * @return \Mocovi\View $this
	 */
	public function addTemplatePool(\DirectoryIterator $resource)
	{
		$this->templates[$resource->getPathname()] = $resource;
		return $this;
	}
}