<?php
namespace Mocovi\Controller;

class Plain extends \Mocovi\Controller
{

	/**
	 * @var array
	 */
	protected static $replacements = array();

	public function setText($text)
	{
		$this->replaceNode($this->dom->createTextNode($text));
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
			self::$replacements = array_merge(self::$replacements, \Mocovi\Input::getInstance()->get); // CAUTION!! Possible HTML-Injection
		}
	}

	public function get(array $params = array())
	{
		$text = $this->node->nodeValue;
		if(preg_match_all('/\$([[:alpha:]_][[:alnum:]_]*)/', $text, $match))
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