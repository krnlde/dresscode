<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Code extends \Mocovi\Controller
{
	protected static $initialize;

	public function setup()
	{
		if (is_null(self::$initialize))
		{
			self::$initialize = new StringAsset('$(function() {prettyPrint()});');
		}
		$this->Application->stylesheet(new FileAsset('applications/common/modules/Code/assets/google-code-prettify/src/prettify.css'));
		$this->Application->javascript(new FileAsset('applications/common/modules/Code/assets/google-code-prettify/src/prettify.js'));
		$this->Application->javascript(self::$initialize); // initialize
	}

	public function get(array $params = array())
	{
		foreach ($this->sourceNode->childNodes as $child)
		{
			if(in_array($child->nodeType, array(XML_CDATA_SECTION_NODE, XML_TEXT_NODE, XML_ELEMENT_NODE)))
			{
				$this->node->appendChild($this->dom->createTextNode($child->nodeValue));
			}
		}
	}
}