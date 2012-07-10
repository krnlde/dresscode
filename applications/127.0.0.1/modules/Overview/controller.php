<?php
namespace Dresscode\Controller;

\Dresscode\Module::requireController('Thumbnails');

class Overview extends \Dresscode\Controller\Thumbnails
{

	/**
	 * @property
	 * @var string
	 */
	protected $source = '.';

	public function get(array $params = array())
	{
		$this->renameNode('thumbnails');
		if ($this->source === '.')
		{
			$this->source = $this->Application->Request->path;
		}
		$xpath = new \DomXpath($this->Application->Model->getDom());
		$xpath->registerNamespace('c', \Dresscode\Controller::NS);
		foreach ($this->Application->Model->getList($this->source) as $path => $element)
		{
			$headline	= $xpath->query('.//c:headline[1]', $element)->item(0);
			$preface	= $xpath->query('.//c:paragraph[1]', $element)->item(0);
			$image		= $xpath->query('(.//c:image|.//c:thumbnail)[1]', $element)->item(0);

			$thumbnail = \Dresscode\Module::createController('thumbnail', null, array
				( 'source' => $image->getAttribute('source')
				)
			);
			$this->addChild($thumbnail);
			if ($headline)
			{
				$headline->setAttribute('priority', 3);
				$thumbnail->addChild(\Dresscode\Module::createControllerFromNode($headline));
			}
			if ($preface)
			{
				$thumbnail->addChild(\Dresscode\Module::createControllerFromNode($preface));
			}

			$anchor = \Dresscode\Module::createController('link', "Read more", array
				( 'to'		=> $path
				, 'class'	=> 'btn btn-primary'
				)
			);
			$thumbnail->addChild($anchor);
			$thumbnail->launch('get', $params);
		}
	}
}