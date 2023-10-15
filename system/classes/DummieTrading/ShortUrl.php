<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;
use HCStudio\Connection;

use JFStudio\Constants;
use BlockChain\Wallet;

class ShortUrl extends Orm {
	protected $tblName = 'short_url';
	const SHORT_LINK_URL = "iam.com.mx";
	const CODE_LENGHT = 6;
	const DEFAULT_TITLE = 'Short Link';
	const DEFAULT_SOURCE = 'iam.com.mx';
	const DEFAULT_DELAY_TIME = 5;
	const DEFAULT_MEDIUM = 'página';	
	const GHOST = 0; // NONTRACKING
	
	public function __construct() {
		parent::__construct();
	}

	public function getDomain()
	{
		return Connection::protocol."://".self::SHORT_LINK_URL;
	}

	public function __constructLinkCode()
	{
		return Connection::protocol."://".$this->_constructLinkCode($this->domain,$this->code);
	}

	public function _constructLinkCode($domain = null,$code = null)
	{
		return $domain."/".$code;
	}

	public function constructLinkCode($code = null)
	{
		return $this->getDomain()."/".$code;
	}

	public function getCode()
	{
		return (new Token)->randomKey(self::CODE_LENGHT);
	}

	public function getShortUrlEWallet(int $user_login_id = null,string $url = null,string $title = null)
	{
		if(isset($user_login_id,$url,$title) === true)
		{
			if($this->getShortUrl($user_login_id,$url,$title))
			{
				return $this->getId();
			}
		}

		return false;
	}

	public function getShortUrl($user_login_id = null,$url = null,$title = null,$code = null)
	{
		if(isset($user_login_id,$url) === true)
		{
			$this->title = $title ? $title : self::DEFAULT_TITLE;
			$this->user_login_id = $user_login_id;

			$this->domain = self::SHORT_LINK_URL;
			$this->url = $url;
			$this->code = isset($code) === true ? $code : $this->getCode();
			$this->create_date = time();
			
			if($this->save())
			{
				return $this->constructLinkCode($this->code);
			}
		}

		return false;
	}

	public function existCode($code = null)
	{
		if(isset($code) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.code
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.code = '{$code}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";
			
			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}

	public function get($short_url_id = null)
	{
		if(isset($short_url_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.code,
						{$this->tblName}.title,
						{$this->tblName}.url,
						{$this->tblName}.ad,
						{$this->tblName}.domain,
						{$this->tblName}.source,
						{$this->tblName}.campaign,
						{$this->tblName}.create_date
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.short_url_id = '{$short_url_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";
			
			return $this->connection()->row($sql);
		}

		return false;
	}

    public function hasShorts(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as shorts
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    HAVING 
                        shorts > 0
                        ";

            return $this->connection()->field($sql) ? true : false;
        }
        return false;
    }

    public function getLink(Wallet $Wallet = null) 
    {
        if(isset($Wallet) === true)
        {
            if(!$Wallet->short_url_id)
            {
				$Wallet = Wallet::constructWalletShortLink($Wallet);
            }

            return $this->_getLink($Wallet->short_url_id);
        }
        return false;
    }
    
    public function _getLink(int $short_url_id = null) 
    {
        if(isset($short_url_id) === true)
        {
            $sql = "SELECT 
                        CONCAT_WS('/',{$this->tblName}.domain,{$this->tblName}.code) as link
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.short_url_id = '{$short_url_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                        ";

            return $this->connection()->field($sql);
        }
        return false;
    }
    
	
	public function getLinkByCode(string $code = null) 
    {
        if(isset($code) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.url,
                        {$this->tblName}.domain,
						{$this->tblName}.code
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.code = '{$code}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                        ";

            return $this->connection()->row($sql);
        }
        return false;
    }
}