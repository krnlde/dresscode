<?php
namespace Mocovi\Controller;

class Link extends \Mocovi\Controller
{

	/**
	 * @var string
	 */
	protected $url;

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
		$this->url = preg_replace('/^([a-z]+)\%3A\/\//', '$1://', $this->url); // recover scheme declaration
		$this->node->setAttribute('url', $this->url);
	}
}