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
	protected static $translation = array
	( 'all' => array
		( 	'loremipsum' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.'
		)
	);

	/**
	 * @param string $language
	 * @return void
	 */
	public static function setLanguage($language)
	{
		if (strlen($language) != 2)
		{
			throw new Exception\NotAllowed('Language code: '.$language);
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
	public static function addTranslation($token, $value, $language = 'all')
	{
		self::$translation[$language][$token] = $value;
	}

	/**
	 * @param array $translations
	 * @param string $language Default: 'all';
	 * @return void
	 */
	public static function addTranslations(array $translations, $language = 'all')
	{
		$newTranslations = array();
		$newTranslations[$language] = $translations;
		self::$translation = array_merge(self::$translation, $newTranslation);
	}

	/**
	 * @param \DomDocument $xml
	 * @return void
	 */
	public static function addTranslationsFromXml(\DomDocument $xml)
	{
		foreach ($xml->getElementsByTagName('token') as $token)
		{
			$name = $token->getAttribute('name');
			foreach ($token->getElementsByTagName('translation') as $translation)
			{
				self::$translation[$translation->getAttribute('lang')][$name] = $translation->nodeValue;
			}
		}
	}

	/**
	 * @param string $token
	 * @return string Value based on the current language {@see self::$language}.
	 */
	public static function translate($token)
	{
		if (isset(self::$translation[self::$language][$token]))
		{
			return trim(self::$translation[self::$language][$token]);
		}
		if (isset(self::$translation['all'][$token]))
		{
			return trim(self::$translation['all'][$token]);
		}
		return $token; // @todo return $token or better an empty string?
	}
}