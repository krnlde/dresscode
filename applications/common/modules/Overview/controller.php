<?php
namespace Dresscode\Controller;

use \Dresscode\Application;

\Dresscode\Module::requireController('Thumbnails');

class Overview extends \Dresscode\Controller\Thumbnails
{

	/**
	 * @property
	 * @var string
	 */
	protected $source = '.';

	/**
	 * @var string
	 */
	private $httpMethod;

	/**
	 * @property
	 * @hidden
	 * @var integer
	 */
	protected $maximum = 5;

	/**
	 * @property
	 * @var string
	 */
	protected $class = 'overview';

	public function post(array $params = array())
	{
		$this->httpMethod = 'post';
		$this->execute($params);
	}

	public function get(array $params = array())
	{
		$this->httpMethod = 'get';
		$this->execute($params);
	}

	protected function execute(array $params = array())
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
			$preface	= $xpath->query('(.//c:paragraph|.//text)[1]', $element)->item(0);
			$image		= $xpath->query('(.//c:image|.//c:thumbnail|.//c:gallery)[1]', $element)->item(0);

			if ($image)
			{
				if ($image->nodeName === 'gallery')
				{
					$thumbnail = $this->loadThumbnailFromGallery($image);
					$thumbnail->setProperty('description', $path);
				}
				else
				{
					$thumbnail = \Dresscode\Module::createController('thumbnail', null, array
						( 'source'		=> $image->getAttribute('source')
						, 'description'	=> $path
						, 'crop'		=> true
						)
					);
				}
			}
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
			$thumbnail->launch($this->httpMethod, $params);
		}
	}

	protected function loadThumbnailFromGallery(\DomElement $gallery)
	{
		\Dresscode\Module::requireController('Gallery');
		$source = $gallery->getAttribute('source');
		$absolutePrefix = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'].Application::basePath());

		if ($source[0] === '/')
		{
			$source = $absolutePrefix.$source;
		}

		if (file_exists($source) && is_dir($source))
		{
			foreach (new \DirectoryIterator($source) as $element)
			{
				if (!$element->isDot() && $element->isFile() && \Dresscode\Controller\Gallery::isImage($element))
				{
					return \Dresscode\Module::createController('thumbnail', null, array
						( 'source'	=> str_replace(array($absolutePrefix, '\\'), array('', '/'), $element->getPathName())
						, 'crop'	=> true
						)
					);
				}
			}
		}
	}
}