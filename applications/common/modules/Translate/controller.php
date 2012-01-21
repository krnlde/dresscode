<?php
namespace Mocovi\Controller;

class_exists('Mocovi\\Controller\\Inline', false) or require __DIR__.'/../Inline/controller.php';

class Translate extends Inline
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
	 * When "true" no words will be cut.
	 *
	 * Requires {@see $cut} to be set.
	 *
	 * @property
	 * @var boolean
	 */
	protected $preserveWords = true;

	public function launchChild(\Mocovi\Controller $child)
	{
		return false;
	}

	protected function get(array $params = array())
	{
		if (!($translation = \Mocovi\Translator::translate($this->token)))
		{
			$this->setText($this->token);
			return;
		}
		if (!empty($this->cut))
		{
			$translatedText = $translation->nodeValue;
			if ($this->preserveWords)
			{
				$words		= explode(' ', $translatedText);
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
				$translatedText = trim(substr($translatedText, 0, $length), ',-');
			}
			else
			{
				$translatedText = rtrim(substr($translatedText, 0, $this->cut));
			}
			$this->setText($translatedText);
		}
		else
		{
			if (array_key_exists($this->token, self::$usedTokens))
			{
				throw new \Mocovi\Exception('Loop detected: '.$this->token);
			}
			self::$usedTokens[$this->token] = null;
			foreach($translation->childNodes as $child)
			{
				if ($controller = \Mocovi\Module::createControllerFromNode($child))
				{
					$controller->launch('get', $params, $this->parentNode, $this->Application);
				}
			}
			self::$usedTokens = array();
		}
	}
}