<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\Package;

class CatalogCampaign extends Orm
{
	protected $tblName = 'catalog_campaign';
	
	const DEFAULT_CAMPAING = 'signup';
	const DEFAULT_CAMPAING_ID = 1;
	const DAYS_TRIAL = 4;
	const OZ_CAMPAIGN = 7;
	public function __construct()
	{
		parent::__construct();
	}

	public static function getPackageIdByCampaign(int $catalog_campaign_id = null)
	{
		if($catalog_campaign_id == self::DAYS_TRIAL)
		{
			return Package::TRIAL; 
		} if($catalog_campaign_id == self::OZ_CAMPAIGN){
			return Package::PREMIUM_ONE_MONTH_OZ_CAMPAIGN; 
		}
	}

	public function getUtm(int $catalog_campaign_id = null)
	{
		if (isset($catalog_campaign_id) === true) {
			$sql = "SELECT 
						{$this->tblName}.utm
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_campaign_id = '{$catalog_campaign_id}'
					AND 
						{$this->tblName}.status = '1'
					";

			if ($utm = $this->connection()->field($sql)) {
				return $utm;
			}
		}

		return self::DEFAULT_CAMPAING;
	}

	public function getCatalogCampaign(string $utm = null)
	{
		if (isset($utm) === true) {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.utm = '{$utm}'
					AND 
						{$this->tblName}.status = '1'
					";

			if ($catalog_campaign_id = $this->connection()->field($sql)) {
				return $catalog_campaign_id;
			}
		}

		return self::DEFAULT_CAMPAING_ID;
	}
	
	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.campaign,
					{$this->tblName}.utm
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";

		return $this->connection()->rows($sql);
	}
}
