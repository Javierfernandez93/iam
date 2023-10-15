<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class CommentPerTask extends Orm {
	protected $tblName = 'comment_per_task';
	public function __construct() {
		parent::__construct();
	}

	public function getAll($task_per_user_id = null,$filter = " AND comment_per_task.reply_comment_per_task_id = '0'") 
	{
		if(isset($task_per_user_id))
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.user_login_id,
						{$this->tblName}.comment, 
						user_data.names,
						user_setting.image,
						{$this->tblName}.create_date
					FROM 
						{$this->tblName} 
					LEFT JOIN
						user_setting
					ON 
						user_setting.user_login_id = {$this->tblName}.user_login_id
					LEFT JOIN
						user_data
					ON 
						user_data.user_login_id = {$this->tblName}.user_login_id
					WHERE 
						{$this->tblName}.task_per_user_id = '{$task_per_user_id}'
					AND 
						{$this->tblName}.status = '1'
						{$filter}
					ORDER BY 
						{$this->tblName}.create_date 
					DESC
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}
}