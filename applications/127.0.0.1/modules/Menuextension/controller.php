<?php
namespace Dresscode\Controller;

use \Dresscode\Event;

class Menuextension extends \Dresscode\Controller
{
	public function get(array $params = array())
	{
		/*
			Here is an example how to select modules in the hierarchy,
			modify them and use events with its callbacks.
		*/
		$this->closest('Menu')->on('addElement', function (\Dresscode\Event $event) {
			$node = $event->relatedTarget;
			if ($node->getAttribute('path') === '/home')
			{
				$menu = $node->ownerDocument->createElement('menu');
				$menu->appendChild($new = $node->cloneNode(true));
				$new->setAttribute('alias', '[Generated]');
				$new->setAttribute('path', 'http://krnl.de/');
				$node->appendChild($menu);
			}
		});
	}
}