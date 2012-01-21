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
 * @package		Mocovi
 */
namespace Mocovi;

/**
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Mocovi
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
		return null; // @todo return $token or better an empty string?
	}
}