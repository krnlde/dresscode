<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Code extends \Mocovi\Controller
{
	public function setup()
	{
		$this->Application->stylesheet(new FileAsset('applications/common/modules/Code/assets/google-code-prettify/src/prettify.css'));
		$this->Application->javascript(new FileAsset('applications/common/modules/Code/assets/google-code-prettify/src/prettify.js'));
		$this->Application->javascript(new StringAsset('$(function() {prettyPrint()});')); // initialize
	}

	public function get(array $params = array())
	{
		foreach ($this->sourceNode->childNodes as $child)
		{
			if($child->nodeType === XML_CDATA_SECTION_NODE)
			{
				$this->node->appendChild($this->dom->createTextNode($child->nodeValue));
			}
		}
	}
}