<?php

namespace JFStudio;

class SymbolParser
{
	public $symbols = null;
	
	public function loadSymbols()
	{
		$this->symbols = json_decode(file_get_contents('../../src/files/symbols/symbols.json'),true);
	}
	
	public static function sanitizePair(string $pair = null)
	{
		return strlen($pair) >= 6 ? substr($pair, 0, 6) : $pair;
	}

	public function getAllSimbolsAsString()
	{
		d($this->symbols);
	}

	public function getSymbol(string $symbol = null)
	{
		if(in_array($symbol,$this->symbols['forex']))
		{
			return $symbol;
		} else if(in_array($symbol,$this->symbols['crypto'])) {
			return $symbol;
		}
	}
}