<?php
/**
 *  Copyright (C) 2011 Kai Dorschner
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @copyright	Copyright 2011, Kai Dorschner
 * @license		http://www.gnu.org/licenses/gpl.html GPLv3
 * @package		Dresscode
 */
namespace Dresscode;

/**
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Dresscode
 */
abstract class Translator
{
	/**
	 * @var string
	 */
	protected static $language = 'en';

	/**
	 * Key-Value based token translations (2D array).
	 *
	 * Structure:
	 * $translation[$language][$token] = $translatedText;
	 *
	 * @var array of array
	 */
	protected static $translation = array();

	/**
	 * @param string $language
	 * @return void
	 */
	public static function setLanguage($language)
	{
		if (strlen($language) != 2)
		{
			throw new Exception\NotAllowed($language);
		}
		self::$language = (string)$language;
	}

	/**
	 * @return string Current language
	 */
	public static function getLanguage()
	{
		return self::$language;
	}

	/**
	 * @param string $token
	 * @param string $value
	 * @param string $language Default: 'all';
	 * @return void
	 */
	public static function addTranslation(\DomNode $token)
	{
		$name = $token->getAttribute('name');
		foreach ($token->getElementsByTagName('translation') as $translation)
		{
			self::$translation[$translation->getAttribute('lang')][$name] = $translation;
		}
	}

	/**
	 * @param \DomDocument $xml
	 * @return void
	 */
	public static function addTranslationsFromXml(\DomDocument $xml)
	{
		foreach ($xml->getElementsByTagName('token') as $token)
		{
			self::addTranslation($token);
		}
	}

	/**
	 * @param string $token
	 * @return \DomNode Value based on the current language {@see self::$language}.
	 */
	public static function translate($token)
	{
		if (isset(self::$translation[self::$language][$token]))
		{
			return self::$translation[self::$language][$token];
		}
		if (isset(self::$translation['all'][$token]))
		{
			return self::$translation['all'][$token];
		}
		return null;
		// return new \DomElement('translation', $token); // @todo return $token or better an empty string?
	}

	/**
	 * @param string $token
	 * @return \DomNode Value based on the current language {@see self::$language}.
	 */
	public static function translateByCount($token, $count)
	{
		$node = self::translate($token)->cloneNode(true); // if not cloned it would be overwritten!
		foreach ($node->childNodes as $child)
		{
			if ($child->nodeType == XML_TEXT_NODE)
			{
				$node->replaceChild(new \DomText(self::getTextByCount($child->nodeValue, $count)), $child);
			}
		}
		return $node;
	}

	/**
	 * Returns singular or plural of a text determined by the value of {@see $count}.
	 *
	 * Example: If you have the text value "tree(s)" and you prepend an amount
	 * like 1 or 2 to it you'll get something like "1 tree(s)" or "2 tree(s)".
	 * This function determines singular and plurals by the amount. This results
	 * in "1 tree" and "2 trees".
	 * The value inside the brackets is the plural appendix.
	 *
	 * @param string $text Unquantified text.
	 * @param integer $count Amount to be quantified.
	 * @return string Singular or plural text.
	 */
	public static function textByCount($text, $count)
	{
		if(strlen($text) > 0)
		{
			preg_match('/\((.+)\)/', $text, $regs, PREG_OFFSET_CAPTURE);
			$fill = '';
			if(isset($regs[1][0]))
			{
				$x = explode('|', $regs[1][0]); // If there are different notations for singular and plural
				if($count != 1)
					$fill = $x[count($x) - 1]; // if is not 1 then choose last element (plural)
				elseif(count($x) > 1)
					$fill = $x[0];
			}
			else
			{
				$regs[0][0] = null;
				$regs[0][1] = null;
			}
			return substr_replace($text, $fill, $regs[0][1], strlen($regs[0][0]));
		}
		return null;
	}
}