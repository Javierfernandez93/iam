<?php

namespace JFStudio;

use DummieTrading\CatalogTradingAccount;

use Exception;

class ParserOrder
{
    const OCO = 'oco';
    const MARKET = 'market';
    const HEADERS = [
        'provider' => 'Provider',
        'symbol' => 'Symbol',
        'stopPrice' => 'Stop loss',
        'price' => 'Price', 
        'stopLimitPrice' => 'Limit', // pe forex
        'priceEntrace' => 'Price entrace', // pe forex
        'takeProfit' => 'Take profit', // tp forex
        'side' => 'Side',
        'comment' => 'Comentario',
    ];

    public static function getBuyHeader(array $data = null) : string 
    {
        if(isset($data['side']))
        {
            if($data['side'] == 'buy') {
                return "📈\n";
            } else if($data['side'] == 'sell') {
                return "📉\n";
            }
        }

        return "";
    }

    public static function getMarketTypeHeader(array $data = null) : string 
    {
        if(isset($data['market_type']))
        {
            if($data['market_type'] == CatalogTradingAccount::BINANCE) {
                return "Market: Crypto";
            } else if($data['market_type'] == CatalogTradingAccount::METATRADER) {
                return "Market: Forex";
            }
        }

        return "";
    }

    public static function parseHeaders(array $data = null) : string 
    {
        $headers = [];

        $headers[] = self::getBuyHeader($data);
        $headers[] = self::getMarketTypeHeader($data);

        foreach($data as $key => $value)
        {
            if(isset($value) && $value)
            {
                if($translatedHeader = self::HEADERS[$key] ?? false)
                {
                    $headers[] = "{$translatedHeader}: {$value}";
                }
            }
        }

        return implode("\n",$headers);
    }

    public static function parse(array $data = null) 
    {
        if($data['type'] == self::OCO) 
        {
            return self::parseHeaders($data);
        } else if($data['type'] == self::MARKET) {
            return self::parseHeaders($data);
        }
	}
}
