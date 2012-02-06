<?php
namespace Mocovi\Controller;

class Menu extends \Mocovi\Controller
{
	/*
		use \Mocovi\Controller\Trait\Param
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

	protected function get(array $params = array())
	{
		// @Test Observer Test
		// $o = new \Mocovi\Observer();
		// $this->attach($o);
		// $o->on('load', function($source) {
		// 	echo 'works.';
		// })
		// ->on('NOTload', function($source) {
		// 	echo 'doesn\'t work.';
		// })
		// ->on('load', function($source) {
		// 	echo 'works too.';
		// });
		// $this->notify('load');
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
		$this->path		= $this->Application->Request->path;

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
			$elementNode->setAttribute('alias', $element->getAttribute('alias'));
			if ($element->getAttribute('modified'))
			{
				$elementNode->setAttribute('modified', $element->getAttribute('modified'));
			}
			if ($this->path === $path)
			{
				$elementNode->setAttribute('active', 1);
			}
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