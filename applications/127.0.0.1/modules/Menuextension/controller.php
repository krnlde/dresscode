<?php
namespace Mocovi\Controller;

use \Mocovi\Event;

class Menuextension extends \Mocovi\Controller
{
	protected function get(array $params = array())
	{
		$this->closest('Menu')->on('addElement', function ($event) {
			if ($event->target->getAttribute('path') === '/home')
			{
				$new = $event->target->cloneNode(true);
				$new->setAttribute('alias', '[Generated]');
				$menu = $event->target->ownerDocument->createElement('menu');
				$menu->appendChild($new);
				$event->target->appendChild($menu);
			}
		});
	}
}