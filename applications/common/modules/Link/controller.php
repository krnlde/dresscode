<?php
namespace Mocovi\Controller;

class Link extends \Mocovi\Controller
{

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @property
	 * @var string
	 */
	protected $to;

	protected function get(array $params = array())
	{
		parent::get($params);
		if ($this->to[0] === '/')
		{
			$this->to = dirname($_SERVER['SCRIPT_NAME']).$this->to;
		}
		if ($this->node->childNodes->length === 0)
		{
			$this->node->appendChild($this->dom->createTextNode($this->to));
		}
		$this->url = implode
			( '/'
			, array_map
				( function($element)
					{
						return urlencode($element);
					}
				, explode
					( '/'
					, $this->to
					)
				)
			);
		$this->url = str_replace('%40', '@', $this->url); // recover mail declaration (from urlencode)
		$this->url = preg_replace('/^([a-z]+)\%3A\/\//', '$1://', $this->url); // recover scheme declaration (from urlencode)
		if (preg_match('/^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$/', $this->url))
		{
			$this->url = 'mailto:'.$this->url;
		}
		$this->node->setAttribute('url', $this->url);
	}
}