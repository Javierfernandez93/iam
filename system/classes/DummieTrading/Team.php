<?php

namespace DummieTrading;

use HCStudio\Orm;

class Team extends Orm {
    protected $tblName  = 'team';
    public static $MAX_USERS_PER_TEAM = 5;
    public static $DELETED = -1;
    public static $INACTIVE = 0;
    public static $ACTIVE = 1;
    public function __construct() {
        parent::__construct();
    }

     public function getAmountOfUsersPerTeam(int $user_login_id = null) {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as amount
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.owner_user_login_id = '{$user_login_id}'";

            return $this->connection()->field($sql);
        }

        return self::$MAX_USERS_PER_TEAM;
    }

    public function isAviableToAddUser(int $user_login_id = null) {
        return $this->getAmountOfUsersPerTeam($user_login_id) < self::$MAX_USERS_PER_TEAM;
    }
    
    public function getAll($owner_user_login_id = null) 
    {
    	if(isset($owner_user_login_id) === true)
    	{
    		$sql = "SELECT 
    					{$this->tblName}.{$this->tblName}_id,
    					{$this->tblName}.status,
                        {$this->tblName}.user_login_id,
                        CONCAT_WS(' ',user_data.names,user_data.last_name) as names,
                        user_login.last_login_date,
                        user_account.skills,
                        user_setting.image
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        user_data
                    ON 
                        user_data.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_account
                    ON 
                        user_account.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_setting
                    ON 
                        user_setting.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_login
                    ON 
                        user_login.user_login_id = {$this->tblName}.user_login_id
    				WHERE
    					{$this->tblName}.owner_user_login_id = '{$owner_user_login_id}'
    				AND
    					{$this->tblName}.status != '".self::$DELETED."'
    					";

    		return $this->connection()->rows($sql);
    	}

    	return false;
    }
    public function getUsersByName($owner_user_login_id = null,$name = null) 
    {
        if(isset($owner_user_login_id,$name) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.user_login_id,
                        user_setting.image,
                        CONCAT_WS(' ',user_data.names,user_data.last_name) as names
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        user_data
                    ON 
                        user_data.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_setting
                    ON 
                        user_setting.user_login_id = {$this->tblName}.user_login_id
                    WHERE
                        user_data.names LIKE '%{$name}%'
                    AND
                        {$this->tblName}.owner_user_login_id = '{$owner_user_login_id}'
                    AND
                        {$this->tblName}.status != '".self::$DELETED."'
                        ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
}