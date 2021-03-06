<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Code extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $language;

	protected static $initialize;

	/**
	 * @var \Dresscode\Pool
	 */
	protected static $Pool;

	public function setup()
	{
		if(is_null(self::$Pool))
		{
			self::$Pool = new \Dresscode\Pool('js');
			self::$Pool->add(new \DirectoryIterator(__DIR__.'/assets/google-code-prettify/src/'));
		}
	}

	public function get(array $params = array())
	{
		if (is_null(self::$initialize))
		{
			self::$initialize = new StringAsset('$(function() {prettyPrint()});');
		}
		// $Application->stylesheet(new FileAsset(__DIR__.'/assets/google-code-prettify/src/prettify.css'));
		$this->Application->stylesheet(new FileAsset(__DIR__.'/assets/prettify.custom.css'));
		$this->Application->javascript(new FileAsset(__DIR__.'/assets/google-code-prettify/src/prettify.js'));
		if ($this->language)
		{
			if($style = self::$Pool->find('lang-'.strtolower($this->language)))
			{
				$this->class .= ' lang-'.strtolower($this->language);
				$this->Application->javascript(new FileAsset($style));
			}
		}
		$this->Application->javascript(self::$initialize); // initialize

		foreach ($this->sourceNode->childNodes as $child)
		{
			// if(in_array($child->nodeType, array(XML_CDATA_SECTION_NODE, XML_TEXT_NODE, XML_ELEMENT_NODE)))
			if($child->nodeType === XML_CDATA_SECTION_NODE) // @todo to be tested
			{
				$this->node->appendChild($this->dom->createTextNode($child->nodeValue));
			}
		}
	}
}