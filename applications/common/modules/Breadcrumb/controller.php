<?php
namespace Dresscode\Controller;


/**
 * @todo howto deal with invisible files?
 */
class Breadcrumb extends \Dresscode\Controller
{
	public function get(array $params = array())
	{
		$path = $this->closest('Root')->getProperty('path');
		if ($path[0] === '/')
		{
			$path = substr($path, 1);
		}
		$elements = explode('/', $path);
		$part = '';
		foreach ($elements as $element)
		{
			$part .= '/'.$element;
      try {
        $file = $this->Application->Model->read($part);
        $this->node->appendChild($e = $this->dom->createElement('element', $file->getAttribute('alias') ?: $file->getAttribute('name')));
        $e->setAttribute('path', $this->Application->basePath().$part);
        if ($element === end($elements))
        {
          $e->setAttribute('active', true);
        }
      } catch (\Dresscode\Exception $e) {
        // No action needed.
      }
		}
	}
}