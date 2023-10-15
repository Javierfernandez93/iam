<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\BannerPerCampaign;

class PrintPerBanner extends Orm
{
	protected $tblName = 'print_per_banner';

	const VIEWED = 1;
	const ROTATED = 2;
	public function __construct()
	{
		parent::__construct();
	}

	public function resetViews(int $user_login_id = null,int $position = null) 
	{
		if($prints = $this->getAllPrintsByUserAndPosition($user_login_id,$position))
		{
			foreach($prints as $print_per_banner_id)
			{
				$PrintPerBanner = new PrintPerBanner;
				
				if($PrintPerBanner->loadWhere('print_per_banner_id = ?',$print_per_banner_id))
				{
					$PrintPerBanner->status = self::ROTATED;
					$PrintPerBanner->save();
				}
			}
		}
	}

	public function hasBannersToShow(int $user_login_id = null,int $position = null) : bool
	{
		$banner_per_campaign_ids = [];
		$amount_of_banners_in_position = (new BannerPerCampaign)->getCountInPosition($position);

		if($banner_per_campaign_ids = $this->getPrintedBannersIds($user_login_id,$position))
		{
			return sizeof($banner_per_campaign_ids) < $amount_of_banners_in_position;	
		}

		return true;
	}

	public function _getNextBanner(int $user_login_id = null,int $position = null) 
	{
		$banner_per_campaign_ids = null;

		if($banner_per_campaign_ids = $this->getPrintedBannersIds($user_login_id,$position))
		{
			$banner_per_campaign_ids = implode(',',$banner_per_campaign_ids);
		}

		if($banner = (new BannerPerCampaign)->getBannerNotIn($banner_per_campaign_ids,$position))
		{
			return $banner;
		}

		return false;
	}	

	public function addPrint(int $user_login_id = null,int $banner_per_campaign_id = null) : bool
	{
		$PrintPerBanner = new PrintPerBanner;
		$PrintPerBanner->user_login_id = $user_login_id;
		$PrintPerBanner->banner_per_campaign_id = $banner_per_campaign_id;
		$PrintPerBanner->create_date = time();

		return $PrintPerBanner->save();
	}

	public function getNextBanner(int $user_login_id = null,int $position = null)  
	{
		if($this->hasBannersToShow($user_login_id,$position) === false)
		{
			$this->resetViews($user_login_id,$position);
		}

		if($banner = $this->_getNextBanner($user_login_id,$position))
		{
			if($this->addPrint($user_login_id,$banner['banner_per_campaign_id']))
			{
				return $banner;
			}
		}

		return false;
	}

	public function getCount(int $banner_per_campaign_id = null) : int
	{
		if (isset($banner_per_campaign_id) === true) 
        {
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) as c
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.banner_per_campaign_id = '{$banner_per_campaign_id}'
					AND 
						{$this->tblName}.status IN ('".self::ROTATED."','".self::VIEWED."')
					";
			return $this->connection()->field($sql);
		}

        return  0;
	}
	
	public function getCountIn(string $banner_per_campaign_ids = null) : int
	{
		if (isset($banner_per_campaign_ids) === true) 
        {
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) as c
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.banner_per_campaign_id IN({$banner_per_campaign_ids})
					AND 
						{$this->tblName}.status IN ('".self::ROTATED."','".self::VIEWED."')
					";

			return $this->connection()->field($sql);
		}

        return  0;
	}
	
	public function getPrintedBannersIds(int $user_login_id = null,int $position = null) 
	{
		if (isset($user_login_id,$position) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.banner_per_campaign_id
					FROM 
						{$this->tblName}
					LEFT JOIN
						banner_per_campaign
					ON
						banner_per_campaign.banner_per_campaign_id = {$this->tblName}.banner_per_campaign_id
					LEFT JOIN
						catalog_banner
					ON
						catalog_banner.catalog_banner_id = banner_per_campaign.catalog_banner_id
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '".self::VIEWED."'
					AND 
						catalog_banner.position = '{$position}'
					";

			return $this->connection()->column($sql);
		}

        return  0;
	}
	
	public function getAllPrintsByUserAndPosition(int $user_login_id = null,int $position = null) 
	{
		if (isset($user_login_id,$position) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					LEFT JOIN
						banner_per_campaign
					ON
						banner_per_campaign.banner_per_campaign_id = {$this->tblName}.banner_per_campaign_id
					LEFT JOIN
						catalog_banner
					ON
						catalog_banner.catalog_banner_id = banner_per_campaign.catalog_banner_id
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '".self::VIEWED."'
					AND 
						catalog_banner.position = '{$position}'
					";

			return $this->connection()->column($sql);
		}

        return  0;
	}
}
