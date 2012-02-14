<?php
namespace Mocovi\Controller;

class Root extends \Mocovi\Controller
{
	/**
	 * You are able to define another "theme" for your website just by changing the CSS folder.
	 *
	 * @todo implement
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
	protected $title;

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

	protected function before(array $params = array())
	{
		$cssPool = new \Mocovi\Pool('css');
		$cssPool->add(new \DirectoryIterator('applications/common/assets/css'));

		// @todo $theme goes here!!

		if (file_exists($custom = 'applications/'.$this->Application->getName().'/assets/css'))
		{
			$cssPool->add(new \DirectoryIterator($custom));
		}

		$this->Application->stylesheets
		(	array
			// (	'applications/common/assets/bootstrap/bootstrap.min.css'
			(	'applications/common/assets/css/less/main-16px.css'
			,	$cssPool->find('main')
			)
		);
		$this->Application->javascripts
		(	array
			(	'applications/common/assets/js/jquery.min.js' // or 'http://code.jquery.com/jquery.min.js'
			,	'applications/common/assets/js/external-links.js'
			)
		);
		parent::before($params);
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
		parent::get($params);
	}
}