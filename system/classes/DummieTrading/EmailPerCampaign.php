<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class EmailPerCampaign extends Orm {
	protected $tblName = 'email_per_campaign';

    const GUEST = 0;
	public function __construct() {
		parent::__construct();
	}

	public static function addEmailRecord(int $user_login_id = null,string $email = null,int $campaign_email_id = null)
	{   
		if(isset($email,$campaign_email_id) === true)
		{
            $EmailPerCampaign = new EmailPerCampaign;

            $EmailPerCampaign->user_login_id = isset($user_login_id) ? $user_login_id : self::GUEST;
            $EmailPerCampaign->email = $email;
            $EmailPerCampaign->campaign_email_id = $campaign_email_id;
            $EmailPerCampaign->create_date = time();
			
			return $EmailPerCampaign->save();
		}

		return false;
	}
	
	public function getAll(int $campaign_email_id = null)
	{   
		if(isset($campaign_email_id) === true)
		{
            $sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.email,
						{$this->tblName}.user_login_id,
						{$this->tblName}.create_date
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.campaign_email_id = '{$campaign_email_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
						";
			
			return $this->connection()->rows($sql);
		}

		return false;
	}
}