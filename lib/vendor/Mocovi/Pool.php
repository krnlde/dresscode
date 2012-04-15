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
 * This is an abstract filesystem pool.
 *
 * It searches for files inside the {@see $pools} and acts as LIFO stack.
 * When a file is not found in the first pool, it continues searching on the second
 * and so on.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
 */
class Pool
{
	/**
	 * LIFO pool-stack.
	 *
	 * @var array of \DirectoryIterator
	 */
	protected $pools	= array();
	protected $cache	= array();
	protected $extension;

	public function __construct($extension = 'php')
	{
		$this->extension = $extension;
	}

	/**
	 * Adds a new pool to the pool-stack.
	 *
	 * @param \DirectoryIterator $directory New Pool
	 * @return \Mocovi\Pool $this
	 */
	public function add(\DirectoryIterator $directory)
	{
		//if ($directory->isDir())
		{
			$this->pools[] = $directory;
		}
		return $this;
	}

	/**
	 * LIFO stack search algorithm.
	 *
	 * @param string $name Filename without extension.
	 * @return string Path
	 */
	public function find($name)
	{
		if (empty($this->cache))
		{
			$this->initialize();
		}
		if (isset($this->cache[$name]))
		{
			return $this->cache[$name]; // Return already used control from cache.
		}
		return null; // not found
	}

	public function initialize()
	{
		for ($i = (count($this->pools) - 1); $i >= 0; $i--) // Search backwards (last added pool is checked first!)
		{
			foreach ($this->pools[$i] as $element)
			{
				if (!$element->isDot())
				{
					$path				= $element->getPathName();
					$extension			= '.'.pathinfo($path, PATHINFO_EXTENSION); // $element->getExtension() // Works only in PHP >= 5.3.6
					$name				= $element->getBaseName($extension);
					if (!array_key_exists($name, $this->cache))
					{
						$this->cache[$name] = $path;
					}
				}
			}
		}
	}
}