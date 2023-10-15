<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;
use DummieTrading\ExternalLogin;
use DummieTrading\ExternalLoginRouter;

class ApiCredential extends Orm {
	protected $tblName = 'api_credential';

	public function __construct() {
		parent::__construct();
	}

	public static function duplicateApi(array $data = null)
	{
		$ApiCredential = new ApiCredential;
		
		if(!$ApiCredential->loadWhere('user_login_id = ?',$data['user_login_id']))
		{
			$ApiCredential->create_date = time();
			$ApiCredential->user_login_id = $data['user_login_id'];
		}
		
		$ApiCredential->api_key = $data['api_key'];
		$ApiCredential->api_secret = $data['api_secret'];

		return $ApiCredential->save();
	}

	public static function generateApis(int $user_login_id = null)
    {
		if($email = (new UserLogin(false,false))->getEmail($user_login_id))
		{
			if($user_login_id_to = ExternalLogin::getExternalUserByEmail($email,ExternalLoginRouter::GET_EXTERNAL_USER_BY_EMAIL))
			{
				if($apiCredential = ExternalLogin::generateApis($user_login_id_to,ExternalLoginRouter::API_GENERATOR))
				{
					return self::duplicateApi(array_merge($apiCredential,['user_login_id' => $user_login_id]));
				}
			}
		}

		return false;
    }
    
	public function getUserLoginId(string $api_key = null,string $api_secret = null)
	{
		if(isset($api_key,$api_secret) === true)
		{
			$sql = "SELECT
					    {$this->tblName}.user_login_id
					FROM
					    {$this->tblName}
					WHERE
					    {$this->tblName}.api_key = '{$api_key}'
					AND 
					    {$this->tblName}.api_secret = '{$api_secret}'
					AND 
					    {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getApiCredentials(int $user_login_id = null)
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT
					    {$this->tblName}.api_key,
					    {$this->tblName}.api_secret
					FROM
					    {$this->tblName}
					WHERE
					    {$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
					    {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

			return $this->connection()->row($sql);
		}

		return false;
	}
}
