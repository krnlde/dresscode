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
		$cssPool = $this->getCssPool();
		if (!$this->theme)
		{
			$this->theme = self::DEFAULT_THEME;
		}
		if ($theme = $cssPool->find($this->theme))
		{
			$this->Application->stylesheet(new FileAsset($theme));
		}

		$this->basepath		= $this->Application->basePath();
		$this->domain		= $this->Application->getName();
		$this->title		= $this->Application->file->getAttribute('alias') ?: $this->Application->file->getAttribute('name'); // @todo test if the title is provided everytime!
		$this->path			= $this->Application->Request->path;
		$this->modified		= $this->Application->Model->lastModified($this->path);
		$this->keywords		= implode(',', $this->Application->Model->keywords($this->path, $this->language));
		$this->scheme		= $this->Application->Request->scheme;
		$this->canonical	= $this->scheme.'://'.$this->domain.($this->Application->Request->port ? ':'.$this->Application->Request->port : '').$this->basepath.$this->path;
		$this->language		= \Dresscode\Translator::getLanguage();
		if ($this->Application->file->getAttribute('author'))
		{
			$this->author	= $this->Application->file->getAttribute('author');
		}

		$this->Application->javascript(new FileAsset('applications/common/assets/bootstrap/js/bootstrap-alert.js')); // @todo temporarily

		$Application = $this->Application;
		$this->on('ready', function ($event) use ($Application) { // @todo you can use $this in anonymous functions directly in PHP 5.4
			$Application->javascript(new FileAsset('applications/common/assets/js/external-links.js')); // load this script at last!
		});

		// $this->addChild(\Dresscode\Module::createController('breadcrumb'));
	}

	protected function getCssPool()
	{
		$cssPool = new \Dresscode\Pool('css');
		$cssPool->add(new \DirectoryIterator('applications/common/assets/css'));

		if (file_exists($custom = 'applications/'.$this->Application->getName().'/assets/css')) // @todo clean this mess up.
		{
			$cssPool->add(new \DirectoryIterator($custom));
		}
		return $cssPool;
	}
}