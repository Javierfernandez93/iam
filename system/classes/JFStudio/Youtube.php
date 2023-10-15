<?php

namespace JFStudio;

use JFStudio\ApiModelErrors; 
use JFStudio\SimpleCipher; 
use HCStudio\Util;

class Youtube {
    private static $instance;
    public static $VIDEO_YOUTUBE_PATH = "https://www.youtube.com/watch?v=";
    public static function getInstance()
    {
        if(!self::$instance instanceof self)
          self::$instance = new self;

        return self::$instance;
    }

    public function videoProcessor($video_url = null) 
    {
        if($this->isVideoFromYoutube($video_url) === true)
        {
            $video_id = $this->getVideoId($video_url);  

            if($this->existVideoOnYoutube($video_id))
            {
                return $this->getVideoMiniature($video_id);
            }
        }   

        return false;
    }

    public function getVideoMiniature($video_url = null) 
    {
        return "http://img.youtube.com/vi/$video_url/sddefault.jpg";
    }

    public function getVideoId($video_url = null) 
    {           
        return substr($video_url, strlen(self::$VIDEO_YOUTUBE_PATH), strlen($video_url));
    }

    public function isVideoFromYoutube($video_url = null) 
    {
        if (strpos($video_url, 'www.youtube.com') !== false) 
        {
            return true;
        }
    }

    public function existVideoOnYoutube($video_id = null) 
    {
        $url = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$video_id&format=json";

        $headers = get_headers($url);

        return substr($headers[0], 9, 3) != "404";
    }
}