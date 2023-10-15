<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class CampaignBannerPerUser extends Orm
{
	protected $tblName = 'campaign_banner_per_user';

	const UNPUBLISHED = 0;
	const PUBLISHED = 1;
	const DELETED = -1;
	public function __construct()
	{
		parent::__construct();
	}

	public function getAll(int $user_login_id = null)
	{
		if (isset($user_login_id) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.name,
						{$this->tblName}.status,
						{$this->tblName}.create_date
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status != '".Constants::DELETE."'
					ORDER BY 
						{$this->tblName}.create_date 
					DESC
					";

			return $this->connection()->rows($sql);
		}
	}
	
	public function getSingle(int $campaign_banner_per_user_id = null)
	{
		if (isset($campaign_banner_per_user_id) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.name,
						{$this->tblName}.status,
						{$this->tblName}.description,
						{$this->tblName}.country_ids,
						{$this->tblName}.create_date
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.campaign_banner_per_user_id = '{$campaign_banner_per_user_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->row($sql);
		}
	}
}
