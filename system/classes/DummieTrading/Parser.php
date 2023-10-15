<?php

namespace DummieTrading;

class Parser {
    const VALID_ARGS = [
        'names',
        'whatsApp',
        'info',
        'gain',
        'bid',
        'ask',
        'url',
        'link',
        'referral_link',
        'drawdown',
        'walletBalance',
        'token',
        'operation',
        'gainPercentaje',
        'orderId',
        'stopPrice',
        'equity',
        'balance',
        'company',
        'symbol',
        'lotage',
        'side',
        'type',
        'login',
        'priceEntrace',
        'takeProfit',
        'profit',
        'stopLoss',
        'signalProvider'
    ];

    const DEFAULT_ARGS = [
        'names' => 'Usuario',
        'whatsApp' => '',
        'symbol' => '',
        'walletBalance' => '',
        'token' => '',
        'drawdown' => '',
        'lotage' => '',
        'side' => '',
        'gain' => '',
        'url' => '',
        'link' => '',
        'referral_link' => '',
        'gainPercentaje' => '',
        'orderId' => '',
        'stopPrice' => '',
        'operation' => '',
        'equity' => '',
        'info' => '',
        'balance' => '',
        'login' => '',
        'type' => '',
        'ask' => '',
        'bid' => '',
        'profit' => '',
        'priceEntrace' => '',
        'signalProvider' => '',
        'takeProfit' => '',
        'stopLoss' => '',
        'company' => 'DummieTrading'
    ];

    public static function sanitize(array $args = null) : array 
    {
        return array_filter($args,function($arg){
            return in_array($arg,self::VALID_ARGS);
        },ARRAY_FILTER_USE_KEY);
    }

    public static function existArg(string $text = null, string $arg = null) : bool
    {   
        return strpos($text, "{{{$arg}}}") !== false;
    }

    public static function doParser(string $text = null,array $args = null) : string
    {
        $args = self::sanitize($args);
        $args = array_merge(self::DEFAULT_ARGS, $args);

        foreach ($args as $key => $arg)
        {
            if(isset($arg))
            {
                if(self::existArg($text,$key))
                {
                    $text = str_replace("{{{$key}}}",$arg,$text);
                }
            }
        }

        return $text;
    }


    public static function parserArray(array $data = null) : string
    {
        $temp = [];

        foreach($data as $key => $value)
        {
            if($value)
            {
                $temp[] = "{$key} : {$value}";
            }
        }


        return implode(PHP_EOL,$temp);
    }

    public static function parserMultidimensionalArray(array $data = null) : string
    {
        $temp = [];

        foreach($data as $array)
        {
            if($array)
            {
                $temp[] = self::parserArray($array);
            }
        }

        return implode(PHP_EOL,$temp);
    }
}