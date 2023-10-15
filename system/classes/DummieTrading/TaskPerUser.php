<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class TaskPerUser extends Orm {
	protected $tblName = 'task_per_user';
	public static $CANCELED = -2;
	public static $STOPED = -1;
	public static $MAKED = 0;
	public static $WORKING = 1;
	public static $DONE = 2;
	public function __construct() {
		parent::__construct();
	}

	public function getStatusByTask($status = null) 
	{
		if($status == self::$MAKED){
			return "Creada";
		} else if($status == self::$DONE) {
			return "Listo";
		} else if($status == self::$WORKING) {
			return "En proceso";
		} else if($status == self::$STOPED) {
			return "Estancado";
		} else if($status == self::$CANCELED) {
			return "Cancelada";
		} 
 	}

 	public function getStatusClass($status = null) 
	{
		if($status == self::$MAKED){
			return "secondary";
		} else if($status == self::$DONE) {
			return "success";
		} else if($status == self::$WORKING) {
			return "primary";
		} else if($status == self::$STOPED) {
			return "danger";
		} else if($status == self::$CANCELED) {
			return "warning";
		} 
 	}

	public function getAll($user_login_id = null) 
	{
		if(isset($user_login_id))
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.user_login_id,
						{$this->tblName}.task, 
						{$this->tblName}.status, 
						{$this->tblName}.create_date, 
						{$this->tblName}.programated_date, 
						user_setting.image,
						catalog_task_urgency.catalog_task_urgency_id,
						catalog_task_urgency.catalog_task_urgency,
						catalog_task.catalog_task
					FROM 
						{$this->tblName} 
					LEFT JOIN
						catalog_task
					ON
						catalog_task.catalog_task_id = {$this->tblName}.catalog_task_id
					LEFT JOIN
						catalog_task_urgency
					ON
						catalog_task_urgency.catalog_task_urgency_id = {$this->tblName}.catalog_task_urgency_id
					LEFT JOIN
						user_setting
					ON
						user_setting.user_login_id = {$this->tblName}.user_login_id
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					ORDER BY 
						{$this->tblName}.programated_date
					ASC
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}
}