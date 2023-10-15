<?php

namespace JFStudio;

use JFStudio\OrderTypeParser;
use JFStudio\OrderKeysIdentificator;
use JFStudio\LotajeParser;

class ExtractVariables
{
	const VARIABLES = [
		'email' => [
			'pattern' => '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i'
		],
		'phone' => [
			'pattern' => '/\\+?[1-9][0-9]{7,14}/'
		],
		'limit' => [
			'pattern' => '/[1-9]{1,2}[.][0-9]{1,4}/'
		],
		'lotage' => [
			'pattern' => '/[0]{1}[.][0-9]{1,4}/'
		],
		'side' => [
			'pattern' => '/(compra|venta|comprar|vender|vende|sell|buy)/i'
		],
	];

	public static function extract(array $data = null) : array
	{
        if(in_array($data['tag'],['set_symbol','set_symbol_order_limit']))
        {
            return [
                'symbol' => OrderKeysIdentificator::identifySymbol($data['message'])
            ];
        } else if(in_array($data['tag'],['set_order_type','set_order_type_order_limit'])) {
            $OrderTypeParser = new OrderTypeParser;
			
			if($type = self::extractVarSingle($data['message'],self::VARIABLES['side']['pattern']))
			{
				$type = $OrderTypeParser->getOrderType(strtoupper($type));

				return [
					'type' => $type,
					'buy' => $OrderTypeParser->getOrderTypeint($type)
				];
			}
        } else if(in_array($data['tag'],['set_lotage','set_lotage_order_limit'])) {
			if($lotage = self::extractVarSingle($data['message'],self::VARIABLES['lotage']['pattern']))
			{
				return [
					'lotage' => $lotage
				];
			}

        } else if(in_array($data['tag'],['set_price_entrace_order_limit'])) {
            return [
                'priceEntrace' => OrderKeysIdentificator::identifyPrice($data['message'])
            ];
        } else if(in_array($data['tag'],['set_take_profit_order_limit'])) {
            return [
                'takeProfit' => OrderKeysIdentificator::identifyPrice($data['message'])
            ];
        } else if(in_array($data['tag'],['set_stop_loss_order_limit'])) {
            return [
                'stopLoss' => OrderKeysIdentificator::identifyPrice($data['message'])
            ];
        }

        return [];
	}

    public static function extractVar(string $message = null,string $pattern) : array|null
	{
		preg_match_all($pattern, $message, $matches);

		return empty($matches[0]) ? null : $matches[0];
	}

    public static function extractVarSingle(string $message = null,string $pattern) : string|bool
	{
		if($matches = self::extractVar($message, $pattern))
		{
			return $matches[0];
		}

		return false;
	}

	public static function identifyWord(array $data = null) : array
	{
		$word = [];

		if($words = explode(" ",$data['message']))
		{
			$index = array_search($data['value'], $words);

			if($index !== false)
			{
				$wordsSliced = array_slice($words, $index - 2, 5);

				$wordsSliced = implode(" ", $wordsSliced);

				$extracted = self::extractVar($wordsSliced,$data['behindWords']['pattern']);

				if($extracted)
				{
					$word = $extracted;
				}
			}
		}


		return $word;
	}

	public static function extractVars(string $message = null) : array
	{
		$extractedVars = [];

		foreach (self::VARIABLES as $variableName => $variable)
		{
			if($values = self::extractVar($message,$variable['pattern']))
			{
                foreach($values as $value)
                {
                    $extractedVars[] = [
                        'variable' => $variableName,
                        'value' => $value
                    ];
                }
			}
		}
	
		return $extractedVars;
	}

	public static function hasVar(array $data = null,string $variable = null) : bool
	{
		$foundVar = array_filter($data,function($var) use($variable) {
			return $var['variable'] == $variable;
		});

		return sizeof($foundVar) > 0;
	}

	public static function getVar(array $data = null,string $variable = null) : string
	{
		$var = array_filter($data,function($var) use($variable) {
			return $var['variable'] == $variable;
		});
	
		return sizeof($var) > 0 ? $var[0]['value'] : '';
	}

	public static function _extract(string $message = null) : array
	{
		return self::extractVars($message);
	}

	public static function extractKeyPair(string $message = null) : array
	{
		$vars = self::extractVars($message);
		$keyPair = [];

		if($vars)
		{
			foreach($vars as $var)
			{
				$keyPair[$var['variable']] = $var['value'];
			}
		}

		return $keyPair;
	}
}