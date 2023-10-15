<?php

namespace JFStudio;

class GeoFence
{
	private static $instance;
	private $auth = "621fa61e-d3f6-4be2-835f-8a939d8f08d1";
	public static function getInstance()
 	{
        if(!self::$instance instanceof self)
            self::$instance = new self;

        return self::$instance;
 	}
 	public function getaddress($lat = false,$lng = false)
    {
        if($lat && $lng)
        {
         $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
         $json = @file_get_contents($url);
         $data=json_decode($json);
         $status = $data->status;

         if($status=="OK") {
           return $data->results[0]->formatted_address;
         }
        }

        return "Sin acceso a la ubicación";
    }

    public function getIpInfo($ip = null)
    {
        if(isset($ip))
        {
            $request_uri = 'http://ipinfo.io/'.$ip;
            $document = file_get_contents($request_uri);
            return json_decode($document);
        }

    }
}

