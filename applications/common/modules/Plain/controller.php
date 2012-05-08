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
			self::$replacements = array
			(	'path'		=> $this->Application->Request->path
			,	'title'		=> $this->Application->file->getAttribute('alias') ?: $this->Application->file->getAttribute('name')
			,	'domain'	=> $this->Application->getName()
			// @todo Put more stuff in here
			);
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