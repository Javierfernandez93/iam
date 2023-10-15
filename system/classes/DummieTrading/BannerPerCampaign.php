<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class BannerPerCampaign extends Orm
{
	protected $tblName = 'banner_per_campaign';
	public function __construct()
	{
		parent::__construct();
	}

	public function getIds(int $campaign_banner_per_user_id = null) 
	{
		if (isset($campaign_banner_per_user_id) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.campaign_banner_per_user_id = '{$campaign_banner_per_user_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->column($sql);
		}
	}
	
	public function getCount(int $campaign_banner_per_user_id = null) 
	{
		if (isset($campaign_banner_per_user_id) === true) 
        {
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) AS c
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.campaign_banner_per_user_id = '{$campaign_banner_per_user_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql);
		}
	}
	
	public function getBanner(int $campaign_banner_per_user_id = null,int $catalog_banner_id = null) 
	{
		if (isset($campaign_banner_per_user_id,$catalog_banner_id) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.source,
						{$this->tblName}.link
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.campaign_banner_per_user_id = '{$campaign_banner_per_user_id}'
					AND
						{$this->tblName}.catalog_banner_id = '{$catalog_banner_id}'
					AND 
						{$this->tblName}.status = '1'
					";
					
			return $this->connection()->row($sql);
		}
	}

	public function getBannerNotIn(string $banner_per_campaign_ids = null,int $position = null) 
	{
		if (isset($position) === true) 
        {
			$filter = isset($banner_per_campaign_ids) && !empty($banner_per_campaign_ids) ? "AND banner_per_campaign.banner_per_campaign_id NOT IN ({$banner_per_campaign_ids})" : '';

			$sql = "SELECT 
						{$this->tblName}.banner_per_campaign_id,
						{$this->tblName}.source,
						{$this->tblName}.link,
						catalog_banner.position
					FROM 
						{$this->tblName}
					LEFT JOIN
						catalog_banner
					ON
						catalog_banner.catalog_banner_id = banner_per_campaign.catalog_banner_id
					LEFT JOIN
						campaign_banner_per_user
					ON
						campaign_banner_per_user.campaign_banner_per_user_id = banner_per_campaign.campaign_banner_per_user_id
					WHERE 
						{$this->tblName}.status = '".Constants::AVIABLE."'
						{$filter}
					AND 
						catalog_banner.position = '{$position}'
					AND 
						campaign_banner_per_user.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->row($sql);
		}

        return  0;
	}

	
	public function getCountInPosition(int $position = null) 
	{
		if (isset($position) === true) 
        {
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) AS c
					FROM 
						{$this->tblName}
					LEFT JOIN
						catalog_banner
					ON
						catalog_banner.catalog_banner_id = banner_per_campaign.catalog_banner_id
					LEFT JOIN
						campaign_banner_per_user
					ON
						campaign_banner_per_user.campaign_banner_per_user_id = banner_per_campaign.campaign_banner_per_user_id
					WHERE 
						catalog_banner.position = '{$position}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					AND 
						campaign_banner_per_user.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql);
		}
	}
}
