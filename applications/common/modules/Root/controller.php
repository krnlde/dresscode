<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Root extends \Mocovi\Controller
{
	const DEFAULT_THEME = 'main';
	/**
	 * You are able to define another "theme" for your website just by changing the CSS folder.
	 *
	 * @property
	 * @hideIfEmpty
	 * @var string
	 */
	protected $theme;

	/**
	 * @property
	 * @var string
	 */
	protected $language;

	/**
	 * @property
	 * @var string
	 */
	protected $title = '';

	/**
	 * @property
	 * @var string
	 */
	protected $author;

	/**
	 * @property
	 * @var string
	 */
	protected $modified;

	/**
	 * @property
	 * @var string
	 */
	protected $canonical;

	/**
	 * @property
	 * @var string
	 */
	protected $path;

	protected function setup()
	{
		$cssPool = $this->getCssPool();
		$this->Application->stylesheet(new FileAsset('applications/common/assets/css/less/main-16px.css'));
		if (!$this->theme)
		{
			$this->theme = self::DEFAULT_THEME;
		}
		if ($theme = $cssPool->find($this->theme))
		{
			$this->Application->stylesheet(new FileAsset($theme));
		}

		$this->Application->javascript(new FileAsset('applications/common/assets/js/jquery.min.js')); // or new HttpAsset('http://code.jquery.com/jquery.min.js');
		$Application = $this->Application;
		$this->on('ready', function ($event) use ($Application) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			$Application->javascript(new FileAsset('applications/common/assets/js/external-links.js')); // load this script at last!
		});
	}

	protected function get(array $params = array())
	{
		$this->canonical	= $params['scheme'].'://'.$params['domain'].($params['port'] ? ':'.$params['port'] : '').$this->Application->basePath().$params['path'];
		$this->language		= \Mocovi\Translator::getLanguage();
		$this->author		= isset($params['author']) ? $params['author'] : '[unknown]';
		$this->title		= isset($params['title']) ? $params['title'] : '[no title provided]';
		$this->modified		= isset($params['modified']) ? $params['modified'] : null;
		$this->domain		= isset($params['domain']) ? $params['domain'] : null;
		$this->path			= isset($params['path']) ? $params['path'] : null;
		$this->node->setAttribute('keywords', implode(',', $this->Application->Model->keywords($this->path, $this->language)));
		parent::get($params);
	}

	protected function getCssPool()
	{
		$cssPool = new \Mocovi\Pool('css');
		$cssPool->add(new \DirectoryIterator('applications/common/assets/css'));

		if (file_exists($custom = 'applications/'.$this->Application->getName().'/assets/css')) // @todo clean this mess up.
		{
			$cssPool->add(new \DirectoryIterator($custom));
		}
		return $cssPool;
	}
}