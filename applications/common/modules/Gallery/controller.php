<?php
namespace Dresscode\Controller;

\Dresscode\Module::requireController('Tabs');

class Gallery extends Tabs
{
	/**
	 * Maximum Images
	 *
	 * @var integer
	 */
	const MAXIMUM = 100;

	/**
	 * @property
	 * @var string
	 */
	protected $source;

	/**
	 * @property
	 * @var integer
	 */
	protected $maximum = 20;

	/**
	 * @property
	 * @var integer
	 */
	protected $perTab = 18;

	/**
	 * @property
	 * @hidden
	 * @var string
	 */
	protected $size = 'medium';

	/**
	 * Supported image types
	 *
	 * @var array<string>
	 */
	protected static $imageTypes = array
		( 'jpg'
		, 'png'
		, 'gif'
		, 'tif'
		, 'bmp'
		);

	public function setup()
	{
		parent::setup();
		$this->maximum = min($this->maximum, self::MAXIMUM);
		$this->Application->javascript(new \Assetic\Asset\StringAsset('
			$(".nav-pills").on("click.gallery", ".first, .next, .last, .previous", function (e) {
				var $this = $(this)
					$target = $this.parent();
				e.preventDefault();
				if ($this.is(".first")) {
					$target = $this.parent().nextAll("li:has(a[data-toggle])").first();
				} else if ($this.is(".previous")) {
					$target = $this.closest("ul").find("li.active").prev("li:has(a[data-toggle])");
				} else if ($this.is(".next")) {
					$target = $this.closest("ul").find("li.active").next("li:has(a[data-toggle])");
				}  else { // last
					$target = $this.parent().prevAll("li:has(a[data-toggle])").first();
				}
				$target.children("a").tab("show");
			});
		'));
	}

	/**
	 * Filters ({@see \Dresscode\Controller\Filter}) can be applied to modify the amount of the resulting images.
	 *
	 * @triggers loadFile
	 * @throws \Dresscode\Exception
	 */
	public function get(array $params = array())
	{
		if (!$this->id)
		{
			$this->id = $this->generateId();
		}

		parent::get($params);
		$this->class = strtolower($this->getName());
		$images = array();
		if ($this->source[0] === '/')
		{
			$this->source = $_SERVER['DOCUMENT_ROOT'].\Dresscode\Application::basePath().$this->source;
		}
		if (file_exists($this->source) && is_dir($this->source))
		{
			foreach (new \DirectoryIterator($this->source) as $element)
			{
				if (!$element->isDot() && $element->isFile() && self::isImage($element))
				{
					$result = $this->trigger('loadFile', $element)->result;
					if (is_null($result) || $result) // if $result is null, no result was returned and therefor ignored, otherwise the result value is considered.
					{
						$images[] = str_replace(array($_SERVER['DOCUMENT_ROOT'].\Dresscode\Application::basePath(), '\\'), array('', '/'), $element->getPathName());
					}
				}
			}
		}
		else
		{
			throw new \Dresscode\Exception('source path "'.$this->source.'" not found.');
		}
		usort($images, function($a, $b) {
			return strnatcmp($a, $b);
		});
		for ($i = 0; $i < min(count($images), $this->maximum); $i++)
		// for ($i = 0; $i < count($images); $i++)
		{
			$controller = \Dresscode\Module::createController('thumbnail', null, array
				( 'source'		=> $images[$i]
				, 'size'		=> $this->size
				, 'group'		=> substr(md5($this->getXpath()), 0, 6)
				, 'description'	=> pathinfo($images[$i], PATHINFO_FILENAME)
				, 'crop'		=> true
				)
			);
			$this->addChild($controller);
			$controller->launch('get', $params);
		}
	}

	/**
	 * @param \DirectoryIterator $element
	 * @return boolean
	 */
	public static function isImage(\DirectoryIterator $element)
	{
		return in_array(pathinfo($element->getFileName(), PATHINFO_EXTENSION), self::$imageTypes);
	}
}