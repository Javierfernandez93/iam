<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\CatalogPermission;

class PermissionPerUserSupport extends Orm {
	protected $tblName = 'permission_per_user_support';
	public function __construct() {
		parent::__construct();
	}

	public function _hasPermission($user_support_id = null,$permission = null) 
	{
        $CatalogPermission = new CatalogPermission;

        return $this->hasPermission($user_support_id,$CatalogPermission->getCatalogPermissionId($permission));
	}

	public function hasPermission($user_support_id = null,$catalog_permission_id = null) 
	{
		if(isset($user_support_id,$catalog_permission_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_support_id = '{$user_support_id}'
					AND 
						{$this->tblName}.catalog_permission_id = '{$catalog_permission_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql) ? true : false;
		}
		
		return false;
	}
	
	public function getPermissions($user_support_id = null) 
	{
		if(isset($user_support_id) === true)
		{
			$sql = "SELECT 
						catalog_permission.permission
					FROM 
						{$this->tblName}
					LEFT JOIN 
						catalog_permission
					ON 
						catalog_permission.catalog_permission_id = {$this->tblName}.catalog_permission_id
					WHERE 
						{$this->tblName}.user_support_id = '{$user_support_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->column($sql);
		}
		
		return false;
	}
}