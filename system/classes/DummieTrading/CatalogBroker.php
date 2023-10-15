<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogBroker extends Orm {
	protected $tblName = 'catalog_broker';
	public function __construct() {
		parent::__construct();
	}
	
	public function getAllByCampaign()
	{
		if($brokers = $this->_getAll())
		{
			return array_map(function($broker){
				$broker['market'] = json_decode($broker['market']);

				return $broker;
			},$brokers);
		}
	}

	public function getAll()
	{
		if($brokers = $this->_getAll())
		{
			return array_map(function($broker){
				$broker['market'] = json_decode($broker['market']);

				return $broker;
			},$brokers);
		}
	}
	
	public function _getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.broker,
					{$this->tblName}.market,
					{$this->tblName}.catalog_campaign_ids,
					{$this->tblName}.image,
					{$this->tblName}.description,
					{$this->tblName}.signup_url
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}
	
	public function getProfileId(string $server = null) : string
	{
		if(isset($server))
		{
			$sql = "SELECT 
						{$this->tblName}.profile_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.server = '{$server}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return '';
	}
}