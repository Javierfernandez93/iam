<?php

namespace DummieTrading;

use HCStudio\Orm;

class MailList extends Orm {
    protected $tblName  = 'mail_list';
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll($user_login_id = null) 
    {
    	if(isset($user_login_id) === true)
    	{
    		$sql = "SELECT 
    					{$this->tblName}.{$this->tblName}_id,
    					{$this->tblName}.list_name,
    					(SELECT COUNT(prospect_per_mail_list.prospect_per_mail_list_id) as amount FROM prospect_per_mail_list WHERE prospect_per_mail_list.mail_list_id =  {$this->tblName}.{$this->tblName}_id ) as amount
    				FROM 
    					{$this->tblName}
    				WHERE
    					{$this->tblName}.user_login_id = '{$user_login_id}'
    				AND
    					{$this->tblName}.status = '1'
    					";

    		return $this->connection()->rows($sql);
    	}

    	return false;
    }
}