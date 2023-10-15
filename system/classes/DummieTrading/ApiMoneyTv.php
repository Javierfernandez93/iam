<?php

namespace DummieTrading;

use JFStudio\Curl;

class ApiDummieTrading {
    const END_POINT = 'http://35.165.246.62:3000/';
    // const END_POINT = 'http://localhost:8888/mizuum/app/services/';

	public function __construct() {
	}

	public static function getUsersUrl()
    {
        return self::END_POINT."user/get";
    }

	public static function getDemoUrl()
    {
        return self::END_POINT."user/demo";
    }
	
    public static function getServiceUrl()
    {
        return self::END_POINT."user/service";
    }

	public static function getUser(string $username = null)
	{
        if(isset($username) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getUsersUrl(), [
                'username' => $username,
            ]);

            $response = $Curl->getResponse();
            
            return $response['s'] == 1 ? true : $response;
        }
        
        return false;
	}
	
    public static function generateDemo(string $username = null)
	{
        if(isset($username) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getDemoUrl(), [
                'username' => $username,
            ]);

            $response = $Curl->getResponse(true);
            
            return $response['s'] == 1 ? $response : false;
        }
        
        return false;
	}

    public static function generateService(string $username = null)
	{
        if(isset($username) === true)
        {
            $Curl = new Curl;      

            $Curl->get(self::getServiceUrl(), [
                'username' => $username,
            ]);

            $response = $Curl->getResponse(true);
            
            return $response['s'] == 1 ? $response : false;
        }
        
        return false;
	}
}
