<?php
namespace Mocovi\Controller;

class Gallery extends \Mocovi\Controller
{
	const MAXIMUM = 100;

	/**
	 * @property
	 * @var string
	 */
	protected $source;

	/**
	 * @property
	 * @hidden
	 * @var integer
	 */
	protected $maximum = 5;

	protected $imageTypes = array
		( 'jpg'
		, 'png'
		, 'gif'
		, 'tif'
		, 'bmp'
		);

	protected function before(array $params = array())
	{
		parent::before($params);
		if ($this->maximum > self::MAXIMUM)
		{
			$this->maximum = self::MAXIMUM;
		}
	}

	protected function get(array $params = array())
	{
		parent::get($params);
		$this->Application->stylesheets(array('applications/common/modules/Gallery/assets/jquery-fancybox/source/jquery.fancybox.css'));
		$this->Application->javascripts(array('applications/common/modules/Gallery/assets/jquery-fancybox/source/jquery.fancybox.pack.js'));
		$this->Application->javascripts(array('applications/common/modules/Gallery/assets/js/initialize.js'));
		$this->class = strtolower($this->getName());
		$images = array();
		if ($this->source[0] === '/')
		{
			$this->source = $_SERVER['DOCUMENT_ROOT'].\Mocovi\Application::basePath().$this->source;
		}
		if (file_exists($this->source) && is_dir($this->source))
		{
			foreach (new \DirectoryIterator($this->source) as $element)
			{
				if (!$element->isDot() && $element->isFile() && $this->isImage($element))
				{
					$result = $this->trigger('loadFile', $element)->result;
					if (is_null($result) || $result)
					{
						$images[] = str_replace(array($_SERVER['DOCUMENT_ROOT'].\Mocovi\Application::basePath(), '\\'), array('', '/'), $element->getPathName());
					}
				}
			}
		}
		else
		{
			throw new \Mocovi\Exception('source path "'.$this->source.'" not found.');
		}
		natsort($images);
		for ($i = 0; $i < min(count($images), $this->maximum); $i++)
		{
			$controller = \Mocovi\Module::createController('image', null, array('source' => $images[$i], 'group' => substr(md5($this->getXpath()), 0, 6)));
			$controller->launch('get', $params, $this->node, $this->Application);
		}
	}

	/**
	 * @param \DirectoryIterator $element
	 * @return boolean
	 */
	protected function isImage(\DirectoryIterator $element)
	{
		return in_array(pathinfo($element->getFileName(), PATHINFO_EXTENSION), $this->imageTypes);
	}
}