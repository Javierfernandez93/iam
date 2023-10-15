<?php

namespace JFStudio;

class PixaBay {
    private $key = "17252054-ba16b968e9756a131476f6800";
    public static $url = "https://pixabay.com/api/"; 
    public static $lang = "es"; 
    public static $PER_PAGE = 10; 
    public function getKey()
    {
        return $this->key;
    }
    public function search($q = null,$page = 1)
    {
        $curl = new Curl;
        $curl->get(self::$url, [
            "key" => $this->getKey(),
            "lang" => self::$lang,
            // "image_type" =>  "all", "photo", "illustration", "vector",
            // "orientation" => "popular",
            // "colors" => "grayscale", "transparent", "red", "orange", "yellow", "green", "turquoise", "blue", "lilac", "pink", "white", "gray", "black", "brown",
            "per_page" => self::$PER_PAGE,
            "page" => $page,
            "order" => "popular",
            "q" => $q
        ]);

        return $curl->getResponse(true);
    }
}