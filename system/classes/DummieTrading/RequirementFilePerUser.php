<?php

namespace DummieTrading;

use HCStudio\Orm;

class RequirementFilePerUser extends Orm {
	protected $tblName = 'requirement_file_per_user';

	public static $MAX_DAYS = 90;
	public function __construct() {
		parent::__construct();
	}
	
	public function getAll($user_login_id = null,$write_data = 0) 
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.image,
						{$this->tblName}.description,
						catalog_requirement_file.requirement_file
					FROM 
						{$this->tblName}
					LEFT JOIN 
						catalog_requirement_file
					ON 
						catalog_requirement_file.catalog_requirement_file_id = {$this->tblName}.catalog_requirement_file_id 
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '1'
					AND 
						catalog_requirement_file.write_data = '{$write_data}'
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}

	public function isExpired($create_date = null) : bool
	{
		if(isset($create_date) === true)
		{
			return time() > strtotime("+".self::$MAX_DAYS." days",$create_date);
		}

		return false;
	}

	public function _getAll($user_login_id = null,$write_data = 0) 
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.image,
						{$this->tblName}.description,
						catalog_requirement_file.catalog_requirement_file_id,
						catalog_requirement_file.requirement_file
					FROM 
						{$this->tblName}
					LEFT JOIN 
						catalog_requirement_file
					ON 
						catalog_requirement_file.catalog_requirement_file_id = {$this->tblName}.catalog_requirement_file_id 
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '1'
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}

	public function getRequirementFile($user_login_id = null,$catalog_requirement_file_id = null) 
	{
		if(isset($user_login_id,$catalog_requirement_file_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.image,
						{$this->tblName}.create_date,
						{$this->tblName}.description
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.catalog_requirement_file_id = '{$catalog_requirement_file_id}'
					AND 
						{$this->tblName}.status = '1'
					";

			return $this->connection()->row($sql);
		}

		return false;
	}
}