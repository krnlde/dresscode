<?php
namespace Mocovi\Controller;

use \Mocovi\Event;

class Menuextension extends \Mocovi\Controller
{
	protected function get(array $params = array())
	{
		/*
			Here is an example how to select modules in the hierarchy,
			modify them and use events with its callbacks.
		*/
		$this->closest('Menu')->on('addElement', function (\Mocovi\Event $event) {
			$node = $event->relatedTarget;
			if ($node->getAttribute('path') === '/home')
			{
				$menu = $node->ownerDocument->createElement('menu');
				$menu->appendChild($new = $node->cloneNode(true));
				$new->setAttribute('alias', '[Generated]');
				$node->appendChild($menu);
			}
		});
	}
}