<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CommentPerUser extends Orm {
	protected $tblName = 'comment_per_user';
	public function __construct() {
		parent::__construct();
	}
	public function getAll($user_login_id = null)
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.comment,
						{$this->tblName}.create_date,
						user_support.names
					FROM 
						{$this->tblName}
					LEFT JOIN 
						user_support
					ON 
						user_support.user_support_id = {$this->tblName}.user_support_id
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '1'
					ORDER BY 
						{$this->tblName}.create_date
					DESC 
					";
			
			return $this->connection()->rows($sql);
		}

		return false;
	}
}