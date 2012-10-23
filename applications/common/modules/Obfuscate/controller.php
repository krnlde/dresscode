<?php
namespace Dresscode\Controller;

\Dresscode\Module::requireController('Plain');

class Obfuscate extends Plain
{
	public function get(array $params = array())
	{
		$this->setText(self::obfuscate($this->getText()));
	}

	private static function obfuscate($string)
	{
		$obfuscated = '';
		foreach (str_split($string) as $character)
		{
			$obfuscated .= '&#'.ord($character).';';
		}
		return $obfuscated;
	}
}