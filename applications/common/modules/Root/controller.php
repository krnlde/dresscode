<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Root extends \Dresscode\Controller
{
	const DEFAULT_THEME = 'base';
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
	protected $basepath = '';

	/**
	 * @property
	 * @var string
	 */
	protected $domain = '';

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
	 * @hideIfEmpty
	 * @var string
	 */
	protected $keywords;


	/**
	 * @property
	 * @var string
	 */
	protected $path;

	public function setup()
	{
		$lessPool = $this->getlessPool();
		if (!$this->theme)
		{
			$this->theme = self::DEFAULT_THEME;
		}
		if ($theme = $lessPool->find($this->theme))
		{
			$this->Application->stylesheet(new FileAsset($theme));
		}

		$this->basepath		= $this->Application->basePath();
		$this->domain		= $this->Application->getName();
		$this->title		= $this->Application->file->getAttribute('alias') ?: $this->Application->file->getAttribute('name'); // @todo test if the title is provided everytime!
		$this->path			= $this->Application->Request->path;
		$this->modified		= $this->Application->Model->lastModified($this->path);
		$this->language		= \Dresscode\Translator::getLanguage();
		$this->keywords		= implode(',', $this->Application->Model->keywords($this->path, $this->language));
		$this->scheme		= $this->Application->Request->scheme;
		$this->canonical	= $this->scheme.'://'.$this->domain.($this->Application->Request->port ? ':'.$this->Application->Request->port : '').$this->basepath.$this->path;
		if ($this->Application->file->getAttribute('author'))
		{
			$this->author	= $this->Application->file->getAttribute('author');
		}

		// $this->Application->javascript(new FileAsset('applications/common/assets/js/jQuery-Animate-Enhanced/scripts/src/jquery.animate-enhanced.js'));
		$this->Application->javascript(new FileAsset($this->Application->getCommonPath().'/assets/bootstrap/js/alert.js')); // @todo This is just a temporarily solution for Exception error pages
		$this->Application->javascript(new FileAsset($this->Application->getCommonPath().'/assets/bootstrap/js/dropdown.js'));

		$Application = $this->Application;
		$this->on('ready', function ($event) use ($Application) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			$Application->javascript(new FileAsset($Application->getCommonPath().'/assets/js/external-links.js')); // load this script at last!
		});

		// $this->addChild(\Dresscode\Module::createController('breadcrumb'));
	}

	protected function getLessPool()
	{
		$lessPool = new \Dresscode\Pool('less');
		$lessPool->add(new \DirectoryIterator($this->Application->getCommonPath().'/assets/less'));

		if (file_exists($custom = $this->Application->getPath().'/assets/less')) // @todo clean this mess up.
		{
			$lessPool->add(new \DirectoryIterator($custom));
		}
		return $lessPool;
	}
}