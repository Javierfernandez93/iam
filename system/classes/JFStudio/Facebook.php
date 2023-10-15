<?php

namespace JFStudio;

use HCStudio\Util;
use JFStudio\Curl;

class Facebook {
    private $sender_id = false; 
    private $message = false; 
    private $access_token = false; 
    public static $HUB_VERIFY_TOKEN = "YOUR_SECRET_TOKEN"; 
    public $url = "https://graph.facebook.com/v2.6/me/messages?access_token="; 
    
    public function __construct() {
    }

    public function getURL()
    {
        return $this->url.$this->access_token;
    }
    public function getPostData()
    {
        return  [
            "recipient" => [
                "id" => $this->getSenderId(),
            ],
            "message" => [
                "text" => $this->getMessage()
            ]
        ];
    }

    public function sendMessage()
    {
        $Curl = new Curl;
        $Curl->setHeader('Content-Type','application/json');
        $Curl->post($this->getURL(), $this->getPostData());

        return $Curl->getResponse(true);
    }

    public function setSenderId($sender_id = null)
    {
        $this->sender_id = $sender_id;
    }

    public function setMessage($message = null)
    {
        $this->message = $message;
    }

    public function setAccessToken($access_token = null)
    {
        $this->access_token = $access_token;
    }

    public function getSenderId()
    {
        return $this->sender_id;
    }
    public function getMessage()
    {
        return $this->message;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

}