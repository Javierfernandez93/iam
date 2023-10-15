<?php

namespace JFStudio;

class ApiWhatsapp 
{
	public static $instance = null;

	const API_WHATSAPP = 'https://api.whatsapp.com/send?';

 	public static function sendMessageToPhone(string $phone = null,string $text = null)
 	{
 		if(isset($phone,$text) === true)
 		{
			return self::API_WHATSAPP.http_build_query([
	 			"phone" => $phone,
	 			"text" => $text
	 		]);
 		}
 	}
 	
	public static function sendMessage(string $text = null)
 	{
 		if(isset($phone,$text) === true)
 		{
			return self::API_WHATSAPP.http_build_query([
	 			"text" => $text
	 		]);
 		}
 	}
}