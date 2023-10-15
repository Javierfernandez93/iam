<?php

namespace HCStudio;
use JFStudio\Cipher;
use HCStudio\ModelTokenErrors;

use Exception;

class Token {
	public $KEY_LENGHT = 16;
	public static $SERIALIZED = 1;
	public static $IV_KEY = "TALENTOUMBRELLA2"; 
	public $params;
	private $dinamic_iv;
	public static $instance = null;

	public static function getInstance()
    {
        if(!self::$instance instanceof self)
          self::$instance = new self;

        return self::$instance;
    }

    public function __construct($dinamic_iv = false)
    {
    	$this->setDinamicIv();
    }

    public function getDinamicIv($dinamic_iv = null)
    {
    	return $this->dinamic_iv;
    }

    public function setDinamicIv($dinamic_iv = null)
    {
    	if(isset($dinamic_iv) === true)
    	{
    		$this->dinamic_iv = $dinamic_iv;
    	}
    }

	# Crea llaves aleatorias
	public static function __randomKey($length = 0) {
		return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), rand(0,$length), $length);
	}

	public function randomKey($length = 0) {
		return $this->_randomKey($length);
	}

	private function _randomKey($length = 0) {
		return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, min(62, max(4, $length)));
	}
	
	public static function randomNumber($length = 0) {
		return substr(str_shuffle('0123456789'), 0, $length);
	}

	# Regenera llaves
	private function _makeKey() {
		return $this->_randomKey($this->KEY_LENGHT);
	}
	private function _makeKeys() {
		return array('keyStart'=>$this->_randomKey($this->KEY_LENGHT),'keyEnd'=>$this->_randomKey($this->KEY_LENGHT));
	}
	# Creamos credenciales
	private function _makeSignature($keys) {
		$key = str_rot13(substr($keys['keyEnd'].$keys['keyStart'], 2, 6));
		$hash = sha1($key.'a20b3bdc88d62f25d41a970cd14640f2c4f0a2cf');
		$signature = sha1('9657f1984b8562eb470e598c585dad310da42fbe'.$hash);
		return $signature;
	}

	public function makeDinamicIvKey() 
	{
		return "2";
	}

	public function getIVKey() 
	{
		if($this->getDinamicIv() === true)
		{
			return $this->randomKey(Cipher::$CIPHER_KEY_LEN);
		} else {
			return self::$IV_KEY;
		}
	}
	
	public function getToken(array $params = null) 
	{
		$key = $this->_makeKey();

		$params["__key__"] = str_rot13($key);
		$params = serialize($params);

		$token = Cipher::encrypt($key,$this->getIVKey(),$params);

		return [
			"key" => $key,
			"token" => $token,
		];
	}

	public function checkToken(array $params = null) 
	{
		if (isset($params['key'],$params['token']) === true) 
		{
			$result = null;
			$params['token'] = str_replace(" ", "+", $params['token']);
			
			if($result = Cipher::decrypt($params['key'],$params['token']))
			{
				$result = unserialize($result);

				if($params['key'] === str_rot13($result['__key__']))
				{
					unset($result['__key__']);

					$this->params = $result;

					return true;
				} else {
					throw new Exception(ModelTokenErrors::$INVALID_TOKEN);
				}
			} else {
				throw new Exception(ModelTokenErrors::$NOT_TOKEN_RESULT);
			}
		} else {
			throw new Exception(ModelTokenErrors::$NOT_TOKEN_PARAMS_VALID);
		}

		return false;
	}
	public function prepareUrl($params) {
		return '?'.http_build_query($params);
	}
}