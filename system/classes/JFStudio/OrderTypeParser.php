<?php

namespace JFStudio;

class OrderTypeParser
{	
    const BUY_TYPES = ['comprar','compra','buy'];
    const SELL_TYPES = ['venta','vender','sell'];

	public function getOrderType(string $order_type = null)
	{
		if(isset($order_type))
		{
			if(in_array(strtolower($order_type),self::BUY_TYPES))
			{
				return 'buy';
			} else if(in_array(strtolower($order_type),self::SELL_TYPES)) {
				return 'sell';
			}
		}
	}
	
	public static function getOrderTextByInt(string $order_type = null) : string
	{
		return $order_type ? 'buy' : 'sell';
	}

	public function getOrderTypeint(string $order_type = null)
	{
		if(isset($order_type))
		{
			if(in_array(strtolower($order_type),self::BUY_TYPES))
			{
				return 1;
			} else if(in_array(strtolower($order_type),self::SELL_TYPES)) {
				return 0;
			}
		}
	}
}