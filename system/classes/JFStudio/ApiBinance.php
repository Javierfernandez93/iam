<?php

namespace JFStudio;

use JFStudio\Curl;

use Exception;

class ApiBinance
{
    // const API_KEY = 'hWqkjcRWCqpJ3aOXlanMUA0akTwKYIua8aPTGE8BsfCmzFLBtnQZeC8o5RzO1kVC';
    // const API_SECRET = 'YnHBC6T9k8mNsY5sQ1X4eq81XKikheyeVv4VNfcOaGoXesNYSbngw0z9yDKXjH6N';
    const API_KEY = '';
    const API_SECRET = '';
	const SERVER_URL = 'http://localhost:3000/';
	// const SERVER_URL = 'http://15.188.134.57:3000/';

    const SIDES = ['BUY','SELL'];

    const URLS = [
        'exchangeInfo' => 'account/exchange/info',
        'createMarketOco' => 'account/order/oco',
        'createMarketOrder' => 'account/order/market',
        'accountGet' => 'account/get',
        'getBinanceTradeFee' => 'account/trade/fee',
        'getBinanceTrades' => 'account/trades',
        'accountGetBalance' => 'account/balance',
        'accountAddTrader' => 'account/add/trader',
        'accountTradesListen' => 'account/trades/listen',
        'orderTest' => 'account/order/test'
    ];

	public static function getDefaultKeys(): array
    {
        return [
            'apiKey' => self::API_KEY,
            'apiSecret' => self::API_SECRET
        ];
    }

	public static function isValidSymbol(string $side = null): bool {
        $symbols = file_get_contents("../../src/files/symbols/symbols.json");

        if($symbols)
        {
            $symbols = json_decode($symbols, true);

            return in_array(strtoupper($side), $symbols);
        }
    }

	public static function isValidSide(string $side = null): bool {
        return in_array(strtoupper($side), self::SIDES);
    }

	public static function getUrl(string $path = null): array|string
    {
        return self::SERVER_URL.self::URLS[$path];
    }

	public static function getExchangeInfo(): array|string
	{
		$Curl = new Curl;
		$Curl->get(self::getUrl('exchangeInfo'),self::getDefaultKeys());

		return $Curl->getResponse(true);
	}
	
    public static function createMarketOrder(array $data = null): array|string
	{
		$Curl = new Curl;

		$Curl->get(self::getUrl('createMarketOrder'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}
    
    public static function createMarketOco(array $data = null): array|string
	{
		$Curl = new Curl;

		$Curl->get(self::getUrl('createMarketOco'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}

    public static function accountGet(array $data = null): array|string
	{
		$Curl = new Curl;

		$Curl->get(self::getUrl('accountGet'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}
    
    public static function accountGetBalance(array $data = null): array|string
	{
		$Curl = new Curl;

		$Curl->get(self::getUrl('accountGetBalance'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}

    public static function getBinanceTradeFee(array $data = null): array|string
	{
		$Curl = new Curl;

		$Curl->get(self::getUrl('getBinanceTradeFee'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}
	
    public static function accountAddTrader(array $data = null): array|string
	{
		$Curl = new Curl;

		$Curl->get(self::getUrl('accountAddTrader'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}

    public static function getBinanceTrades(array $data = null): array|string
	{
		$Curl = new Curl;

		$Curl->get(self::getUrl('getBinanceTrades'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}
    
	public static function accountTradesListen(array $data = null): array|string
	{
		$Curl = new Curl;;
		$Curl->get(self::getUrl('accountTradesListen'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}

	public static function orderTest(array $data = null): array|string
	{
		$Curl = new Curl;;
		$Curl->get(self::getUrl('orderTest'),[...self::getDefaultKeys(),...$data]);

		return $Curl->getResponse(true);
	}

    public static function getAllCriptoSymbols(): array {
        return json_decode(file_get_contents('../../src/files/symbols/symbols.json'), true);
    }
}
