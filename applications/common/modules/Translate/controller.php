<?php
namespace Mocovi\Controller;

class_exists('Mocovi\\Controller\\Inline', false) or require __DIR__.'/../Inline/controller.php';

class Translate extends Inline
{
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
		$translatedText = \Mocovi\Translator::translate($this->token);
		if (!empty($this->cut))
		{
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
		}
		$this->setText($translatedText);
	}
}