<?php
namespace Mocovi\Controller;

class Inline extends \Mocovi\Controller
{

	public function setText($text)
	{
		$this->replaceNode($this->dom->createTextNode($text));
	}

	protected function createNode()
	{
		return $this->dom->createTextNode($this->sourceNode->nodeValue);
	}

	protected function get(array $params = array())
	{
		// parent::get(); // prevent class from loading children, since it's an inline element.
		$text = $this->node->nodeValue;
		if(preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)/', $text, $match))
		{
			if(isset($match[1]))
			{
				$matches = array_flip($match[1]);
				foreach ($matches as $var => $key)
				{
					if(isset($params[$var]))
					{
						$text = preg_replace('/\$'.$var.'/', $params[$var], $text);
					}
				}
				$this->setText($text); // @todo maybe use DOMCharacterData::replaceData ( int $offset , int $count , string $data );
			}
		}
	}
}