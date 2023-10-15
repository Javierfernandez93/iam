<?php

namespace DummieTrading;

use HCStudio\Orm;

class CampaignTelegram extends Orm {
	protected $tblName = 'campaign_telegram';
	public function __construct() {
		parent::__construct();
	}

	public function getCatalogPermissionId($permission = null)
	{
		if(isset($permission) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.permission = '{$permission}'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.title,
					{$this->tblName}.create_date
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}

	public function exist(string $permission = null) : bool
	{
		if(isset($permission) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.permission = '{$permission}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}

	public function get(int $campaign_telegram_id = null) 
	{
		if(isset($campaign_telegram_id) === true)
		{
			if($campaign = $this->_get($campaign_telegram_id))
			{
				$campaign['catalog_campaign_ids'] = json_decode($campaign['catalog_campaign_ids'],true);
				$campaign['catalog_campaign_ids_in'] = implode(",",$campaign['catalog_campaign_ids']);

				return $campaign;
			}
		}

		return false;
	}
	
	public function _get(int $campaign_telegram_id = null) 
	{
		if(isset($campaign_telegram_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.catalog_campaign_ids,
						{$this->tblName}.title,
						{$this->tblName}.content
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.campaign_telegram_id = '{$campaign_telegram_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->row($sql);
		}

		return false;
	}
}