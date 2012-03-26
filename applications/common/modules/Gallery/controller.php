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

	/**
	 * @property
	 * @hidden
	 * @var string
	 */
	protected $size = 'medium';

	protected $imageTypes = array
		( 'jpg'
		, 'png'
		, 'gif'
		, 'tif'
		, 'bmp'
		);

	public function setup()
	{
		if ($this->maximum > self::MAXIMUM)
		{
			$this->maximum = self::MAXIMUM;
		}
	}

	/**
	 * Filters ({@see \Mocovi\Controller\Filter}) can be applied to modify the amount of the resulting images.
	 *
	 * @triggers loadFile
	 * @throws \Mocovi\Exception
	 */
	public function get(array $params = array())
	{
		parent::get($params);
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
					if (is_null($result) || $result) // if $result is null, no result was returned and therefor ignored, otherwise the result value is considered.
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
		usort($images, function($a, $b) {
			return strnatcmp($a, $b);
		});
		for ($i = 0; $i < min(count($images), $this->maximum); $i++)
		{
			$controller = \Mocovi\Module::createController('thumbnail', null, array
				( 'source'		=> $images[$i]
				, 'size'		=> $this->size
				, 'group'		=> substr(md5($this->getXpath()), 0, 6)
				, 'description'	=> pathinfo($images[$i], PATHINFO_FILENAME)
				)
			);
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