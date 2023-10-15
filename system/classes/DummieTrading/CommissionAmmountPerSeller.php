<?php

namespace DummieTrading;

use HCStudio\Orm;

class CommissionAmmountPerSeller extends Orm {
	protected $tblName = 'commission_ammount_per_seller';

	public function __construct() {
		parent::__construct();
	}
	
	public function getAll($user_support_id = null) 
	{
		if(isset($user_support_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.ammount,
						catalog_commission.is_percentaje,
						catalog_commission.commission
					FROM 
						{$this->tblName}
					LEFT JOIN 
						catalog_commission
					ON 
						catalog_commission.catalog_commission_id = {$this->tblName}.catalog_commission_id
					WHERE 
						{$this->tblName}.user_support_id = '{$user_support_id}'
					AND 
						{$this->tblName}.status = '1'
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}

	public function get($user_support_id = null,$catalog_commission_id = null) 
	{
		if(isset($user_support_id,$catalog_commission_id) === true)
		{
			$sql = "SELECT
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.ammount,
						catalog_commission.is_percentaje,
						catalog_commission.commission
					FROM 
						{$this->tblName}
					LEFT JOIN 
						catalog_commission
					ON 
						catalog_commission.catalog_commission_id = {$this->tblName}.catalog_commission_id
					WHERE 
						{$this->tblName}.user_support_id = '{$user_support_id}'
					AND 
						{$this->tblName}.catalog_commission_id = '{$catalog_commission_id}'
					AND 
						{$this->tblName}.status = '1'
					";

			return $this->connection()->row($sql);
		}

		return false;
	}
}