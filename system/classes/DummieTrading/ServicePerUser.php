<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class ServicePerUser extends Orm {
	protected $tblName = 'service_per_user';

	const CONFIGURATED = 1;
	public function __construct() {
		parent::__construct();
	}

	public static function makeService(int $user_login_id = null)
	{
		$ServicePerUser = new ServicePerUser;
		
		if(!$ServicePerUser->loadWhere('user_login_id = ?',$user_login_id))
		{
			$ServicePerUser->user_login_id = $user_login_id;
			$ServicePerUser->configurate = self::CONFIGURATED;
			$ServicePerUser->create_date = time();
		}
		
		return $ServicePerUser->save();
	}

	public function hasService(int $user_login_id = null)
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT
					    {$this->tblName}.{$this->tblName}_id 
					FROM
					    {$this->tblName}
					WHERE
					    {$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
					    {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}
}
