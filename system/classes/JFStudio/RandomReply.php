<?php

namespace JFStudio;

class RandomReply
{
	public $array = null;
	
	public static function getRandomReply(string $key = null) : string
    {
        $RandomReply = new self;
        $RandomReply->loadJson();

        return $RandomReply->_getRandomReply($key);
    }

	public function loadJson()
	{
		$this->array = json_decode(file_get_contents('../../src/files/replys/replys.json'),true);
	}
	
	public function _getRandomReply(string $key = null) : bool|string
	{
        if($this->array)
        {
            if(isset($this->array[$key]))
            {
                $keyRandom = rand(0,sizeof($this->array[$key])-1);

                return $this->array[$key][$keyRandom];
            }
        }

        return false;
	}
}