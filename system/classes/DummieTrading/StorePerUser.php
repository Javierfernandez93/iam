<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

class StorePerUser extends Orm {
	protected $tblName = 'store_per_user';
	public static $DELETED = -1;
	public static $INACTIVE = 0;
	public static $ACTIVE = 1;
	public function __construct() {
		parent::__construct();
	}

	public function getStatus($proyect_id = null)
	{
		if(isset($proyect_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.status
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.proyect_id = '{$proyect_id}'
					";
			
			return $this->connection()->field($sql);
		}
		
		return false;
	}

	public function getProyectId($store_per_user_id = null)
	{
		if(isset($store_per_user_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.proyect_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.store_per_user_id = '{$store_per_user_id}'
					";
			
			return $this->connection()->field($sql);
		}
		
		return false;
	}

	public function getStoreNameByStorePerUserId($store_per_user_id = null)
	{
		if(isset($store_per_user_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.name
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.store_per_user_id = '{$store_per_user_id}'
					";
			
			return $this->connection()->field($sql);
		}
		
		return false;
	}

	public function getAllStores()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.name,
					{$this->tblName}.image
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}

	public function hasStore($proyect_id = null)
	{
		if(isset($proyect_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.proyect_id = '{$proyect_id}'
					";
			
			return $this->connection()->field($sql) ? true : false;
		}
		
		return false;
	}
}