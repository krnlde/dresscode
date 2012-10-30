<?php
namespace Dresscode\Controller;

use \Dresscode\Event;

class Menu extends \Dresscode\Controller
{
	/*
		use \Dresscode\Controller\Trait\Param
		to add parameter methods like getParam($x);
	*/
	const MIN_DEPTH = 0;
	const MAX_DEPTH = 10;

	/**
	 * @property
	 * @var string
	 */
	protected $source = '/';

	/**
	 * @property
	 * @hideIfEmpty
	 * @var int
	 */
	protected $depth;

	/**
	 * Trace to the current path
	 * @var array
	 */
	protected $path;

	public function get(array $params = array())
	{
		if (!is_null($this->depth))
		{
			if ($this->depth < self::MIN_DEPTH)
			{
				$this->depth = self::MIN_DEPTH;
			}
			elseif ($this->depth > self::MAX_DEPTH)
			{
				$this->depth = self::MAX_DEPTH;
			}
		}
		else
		{
			$this->depth = self::MAX_DEPTH;
		}
		$this->path = $this->Application->Request->path;

		if ($this->source === '.')
		{
			$this->source = $this->path;
		}
		$list = $this->Application->Model->getList($this->source);
		$this->buildMenuIn($this->node, $list);
	}

	/**
	 * Fills the given $node with submenu entries available from $list.
	 *
	 * The maximum recursion depth is bound to {@see $depth}.
	 *
	 * @param \DomElement $node
	 * @param array $list
	 * @return void
	 */
	protected function buildMenuIn(\DomElement $node, array $list)
	{
		static $depth = 1;
		foreach ($list as $path => $element)
		{
			$node->appendChild($elementNode = $this->dom->createElement('element'));
			$elementNode->setAttribute('path', $path);
			$elementNode->setAttribute('name', $element->getAttribute('name'));
			$elementNode->setAttribute('alias', $element->getAttribute('alias') ?: $element->getAttribute('name'));
			$elementNode->setAttribute('modified', $this->Application->Model->lastModified($path));
			if ($this->path === $path)
			{
				$elementNode->setAttribute('active', 1);
			}
			$this->trigger('addElement', $elementNode);
			if ($depth <= $this->depth)
			{
				$list = $this->Application->Model->getList($path);
				if (count($list) > 0)
				{
					$depth++;
					$elementNode->appendChild($subNode = $this->dom->createElement(strtolower($this->getName())));
					$this->buildMenuIn($subNode, $list);
					$depth--;
				}
			}
		}
	}
}