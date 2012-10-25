<?php
namespace Dresscode\Controller;

\Dresscode\Module::requireController('Plain');

class Translate extends Plain
{
	/**
	 * This variable is used to track used tokens to avoid loop-cycles.
	 *
	 * @var array
	 */
	private static $usedTokens = array();

	/**
	 * @property
	 * @var string
	 */
	protected $token;

	/**
	 * Retrieve only the substring from 0 to $cut.
	 *
	 * @property
	 * @var int
	 */
	protected $cut;

	/**
	 * @property
	 * @var int
	 */
	protected $count;

	/**
	 * When "true" no words will be cut.
	 *
	 * Requires {@see $cut} to be set.
	 *
	 * @property
	 * @var boolean
	 */
	protected $preserveWords = true;


	public function get(array $params = array())
	{
		if (!($translation = \Dresscode\Translator::translate($this->token))) // shortcut if translation is not found
		{
			$this->setText($this->token);
			return;
		}

		$this->params = $params;

		if (count($this->children))
		{
			$this->setProperty('count', $this->children[0]->getNode()->nodeValue);
		}

		if (is_null($this->count))
		{
			$text = trim($translation->nodeValue);
		}
		else
		{
			$text = sprintf(\Dresscode\translator::textByCount($translation->nodeValue, $this->count), $this->count);
		}

		if (!is_null($this->cut))
		{
			if ($this->preserveWords)
			{
				$words		= explode(' ', $text);
				$length		= 0;
				foreach ($words as $word)
				{
					if ($length + strlen($word) > $this->cut)
					{
						if ($length > 0)
						{
							$length -= 1; // strip the last whitespace
						}
						break;
					}
					$length += strlen($word) + 1; // add a whitespace
				}
				$this->cut = $length;
			}
			$this->setText(substr($text, 0, $this->cut));
			return;
		}
		elseif (!is_null($this->count))
		{
			$this->setText($text);
			return;
		}

		if (array_key_exists($this->token, self::$usedTokens))
		{
			throw new \Dresscode\Exception('Loop detected: '.$this->token);
		}

		self::$usedTokens[$this->token] = null; // required in order to nest translate controllers

		foreach($translation->childNodes as $child)
		{
			if ($controller = \Dresscode\Module::createControllerFromNode($child, $this))
			{
				$this->addChild($controller); // this is required so the child knows its parent before the launch
				$controller->launch(__FUNCTION__, $this->params);
			}
			$this->parentNode->insertBefore($controller->getNode(), $this->node);
		}
		self::$usedTokens = array();
	}
}