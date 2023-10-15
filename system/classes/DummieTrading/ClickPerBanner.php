<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class ClickPerBanner extends Orm
{
	protected $tblName = 'click_per_banner';
	public function __construct()
	{
		parent::__construct();
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
						{$this->tblName}.status = '".Constants::AVIABLE."'
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
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql);
		}

        return  0;
	}
}
