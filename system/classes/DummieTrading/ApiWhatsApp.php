<?php

namespace DummieTrading;

use HCStudio\Util;

use JFStudio\Curl;

use DummieTrading\ApiCredential;

class ApiWhatsApp {
    const END_POINT = 'https://www.mizuum.com/app/services/';
    // const END_POINT = 'http://localhost:8888/mizuum/app/services/';

	public function __construct() {
	}

	public static function getSendWhatsAppUrl()
    {
        return self::END_POINT."sendWhatsApp.php";
    }

	public static function sanitizeContact(array $data = null)
    {
        if($data['phone'])
        {
            $data['phone'] = Util::getNumbers($data['phone']);
        }

        return $data;
    }

	public static function sendWhatsAppMessage(array $data = null,int $api_credentials_id = 1)
	{
        if(isset($data) === true)
        {
            $data['contact'] = self::sanitizeContact($data['contact']);

            if(Util::isValidPhone($data['contact']['phone']))
            {
                $Curl = new Curl;            

                $Curl->get(self::getSendWhatsAppUrl(), array_merge([
                    'id' => $data['id'] ?? null,
                    'id' => $data['id'] ?? null,
                    'message' => $data['message'],
                    'image' => $data['image'],
                    'contact' => self::sanitizeContact($data['contact'])
                ],(new ApiCredential)->getApiCredentials($api_credentials_id)));

                return $Curl->getResponse(true);
            }

            return false;
        }
        
        return false;
	}
}
