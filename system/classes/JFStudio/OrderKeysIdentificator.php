<?php

namespace JFStudio;

use HCStudio\Util;

use JFStudio\SymbolParser;

class OrderKeysIdentificator
{
	public static function extractSymbol(string $string = null) : string|bool
	{
		$words = explode(" ",trim($string));
		$foundKey = -1;

		$SymbolParser = new SymbolParser;
		$SymbolParser->loadSymbols();

		$symbols = $SymbolParser->symbols['forex'];

		foreach($words as $word)
		{
			if($key = array_search(strtoupper($word),$symbols))
			{
				$foundKey = $key;
				break;
			}
		}

		return isset($symbols[$foundKey]) ? $symbols[$foundKey] : false;
  	}

    public static function identifySymbol(string $string = null) : string
	{
		if($symbol = self::extractSymbol($string))
		{
			return $symbol;
		}

		return "";
	}

    public static function identifyOrderId(string $string = null) : string
	{
		if($oderId = Util::getNumbersWithFloat($string))
		{
			return $oderId;
		}

		return "";
	}
   
	public static function identifyPrice(string $string = null) : string
	{
		if($price = Util::getNumbersWithFloat($string))
		{
			return $price;
		}

		return "";
	}
	
    public static function identifyKeysPlaceOrder(array $data = null) : array
	{
		$keys = [];

		foreach ($data as $key => $value) {
			if (filter_var($value, FILTER_VALIDATE_INT)) {
				$keys['message_id'] = $data[$key];
			} else {
				$keys['message'] = $data[$key];

				# extracting floats or integers
				if($quantity = Util::getNumbersWithFloat($data[$key]))
				{
					$keys['quantity'] = $quantity;
				}
			}
		}

		return $keys;
	}

    public static function identifyKeysOco(array $data = null)
	{
		return [
			'operation' => $data[0],
			'lotage' => $data[1],
			'takeProfit' => $data[2],
			'stopPrice' => $data[3],
			'symbol' => $data[4],
		];
	}

    public static function identifyKeys(array $data = null)
	{
		$keys = [];

		$SymbolParser = new SymbolParser;
		$SymbolParser->loadSymbols();

		if(sizeof($data) == 2 || sizeof($data) == 3)
		{
			foreach ($data as $key => $value) {
				if (filter_var($value, FILTER_VALIDATE_FLOAT)) {
					$keys['lotage'] = $data[$key];
				} else if (in_array(strtolower($value), ['buy', 'sell', 'compra', 'venta']) == true) {
					$keys['operation'] = $data[$key];
				} else if (in_array(strtoupper($value), $SymbolParser->symbols['forex']) == true) {
					$keys['symbol'] = $data[$key];
				} else if (in_array(strtoupper($value), $SymbolParser->symbols['crypto']) == true) {
					$keys['symbol'] = $data[$key];
				}
			}
		} else {
			$keys['operation'] = $data[0];
			$keys['lotage'] = $data[1];
			$keys['takeProfit'] = $data[2];
			$keys['stopPrice'] = $data[3];
			$keys['symbol'] = $data[4];
		}
		
		return $keys;
	}
}
