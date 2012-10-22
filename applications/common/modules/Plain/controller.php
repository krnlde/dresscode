<?php
namespace Dresscode\Controller;

class Plain extends \Dresscode\Controller
{

	/**
	 * @var array
	 */
	protected static $replacements = array();

	public function setText($text)
	{
		$this->replaceNode($this->dom->createTextNode($text));
	}

	public function getText()
	{
		return $this->node->nodeValue;
	}

	protected function createNode()
	{
		return $this->dom->createTextNode($this->sourceNode->nodeValue);
	}

	public function setup()
	{
		if (!count(self::$replacements))
		{
			if ($root = $this->closest('Root'))
			{
				self::$replacements = $this->closest('Root')->getProperties(); // adopts all Root-Controller attributes as $-replacements
			}
			self::$replacements = array_merge(self::$replacements, \Dresscode\Input::getInstance()->get); // CAUTION!! Possible HTML-Injection
		}
	}

	public function get(array $params = array())
	{
		$text = $this->getText();
		if(preg_match_all('/\$([[:alpha:]_][[:alnum:]_]*)/', $text, $match)) // find replacement vars (@see $replacements)
		{
			if(isset($match[1]))
			{
				$matches = array_flip($match[1]);
				foreach ($matches as $var => $key)
				{
					if(isset(self::$replacements[$var]))
					{
						$text = preg_replace('/\$'.$var.'/', self::$replacements[$var], $text);
					}
				}
				$this->setText($text); // @todo maybe use DOMCharacterData::replaceData ( int $offset , int $count , string $data );
			}
		}
	}
}